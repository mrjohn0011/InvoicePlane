<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

define("OVERTIME_BEFORE_ACTIONS", "5"); //Count of days after the overdue date but before actions execution
define("STATUS_DRAFT", 1);

class Cron extends Base_Controller
{
    private function loadDependencies(){
        $this->load->model('invoices/mdl_invoices_recurring');
        $this->load->model('invoices/mdl_invoices');
        $this->load->helper('mailer');
        $this->load->helper('template');
    }

    private function runActionsOnOverdue(){
        $overdue_invoices = $this->mdl_invoices->is_overdue()->get()->result();

        foreach($overdue_invoices as $invoice){
            $overdue_actions = $invoice->invoice_custom_actions_on_overdue;
            if (!$overdue_actions || $invoice->days_overdue !== OVERTIME_BEFORE_ACTIONS) continue;

            preg_match_all("/[a-z0-9_-]+\\(('[a-z0-9_-]*',?\\s*)*\\)/i", $overdue_actions, $actions);
            $date = date("Y-m-d H:i:s");

            foreach($actions[0] as $action){
                $this->db->insert('ip_action_queue', array(
                    'action' => $action,
                    'planned' => $date
                ));
            }
        }
    }

    public function recur($cron_key = NULL){
        // Check the provided cron key
        if ($cron_key != $this->mdl_settings->setting('cron_key')) {
            if (IP_DEBUG) log_message('error', 'Wrong cron key provided!');
            exit('Wrong cron key!');
        }

        $this->loadDependencies();
        $this->runActionsOnOverdue();

        // Gather a list of recurring invoices to generate
        $invoices_recurring = $this->mdl_invoices_recurring->active()->get()->result();

        foreach ($invoices_recurring as $invoice_recurring) {
            // This is the original invoice id
            $source_id = $invoice_recurring->invoice_id;

            // This is the original invoice
            // $invoice = $this->db->where('ip_invoices.invoice_id', $source_id)->get('ip_invoices')->row();
            $invoice = $this->mdl_invoices->get_by_id($source_id);

            if ($invoice->invoice_status_id == STATUS_DRAFT){
                $target_id = $source_id;
            } else {
                // Create the new invoice
                $db_array = array(
                    'client_id' => $invoice->client_id,
                    'invoice_date_created' => $invoice_recurring->recur_next_date,
                    'invoice_date_due' => $this->mdl_invoices->get_date_due($invoice_recurring->recur_next_date),
                    'invoice_group_id' => $invoice->invoice_group_id,
                    'user_id' => $invoice->user_id,
                    'invoice_number' => $this->mdl_invoices->get_invoice_number($invoice->invoice_group_id),
                    'invoice_url_key' => $this->mdl_invoices->get_url_key(),
                    'invoice_terms' => $invoice->invoice_terms
                );

                $target_id = $this->mdl_invoices->create($db_array, false);
                $this->mdl_invoices->copy_invoice($source_id, $target_id);
            }

            // Update the next recur date for the recurring invoice
            $this->mdl_invoices_recurring->set_next_recur_date($invoice_recurring->invoice_recurring_id);

            // Email the new invoice if applicable
            if ($this->mdl_settings->setting('automatic_email_on_recur') && mailer_configured()) {
                $new_invoice = $target_id === $source_id ? $invoice : $this->mdl_invoices->get_by_id($target_id);

                // Set the email body, use default email template if available
                $this->load->model('email_templates/mdl_email_templates');

                $email_template_id = select_email_invoice_template($new_invoice);
                if (!$email_template_id) {
                    return;
                }

                $email_template = $this->mdl_email_templates->where('email_template_id', $email_template_id)->get();
                if ($email_template->num_rows() == 0) {
                    return;
                }

                $tpl = $email_template->row();

                // Prepare the attachments
                $this->load->model('upload/mdl_uploads');
                $attachment_files = $this->mdl_uploads->get_invoice_uploads($target_id);

                // Prepare the body
                $body = $tpl->email_template_body;
                if (strlen($body) != strlen(strip_tags($body))) {
                    $body = htmlspecialchars_decode($body);
                } else {
                    $body = htmlspecialchars_decode(nl2br($body));
                }

                $from = !empty($tpl->email_template_from_email) ?
                    array($tpl->email_template_from_email, $tpl->email_template_from_name) :
                    array($invoice->user_email, "");

                $subject = !empty($tpl->email_template_subject) ?
                    $tpl->email_template_subject :
                    trans('invoice') . ' #' . $new_invoice->invoice_number;

                $pdf_template = $tpl->email_template_pdf_template;
                $to = $invoice->client_email;
                $cc = $tpl->email_template_cc;
                $bcc = $tpl->email_template_bcc;

                if (email_invoice($target_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files)) {
                    $this->mdl_invoices->mark_sent($target_id);
                    $this->mdl_invoice_amounts->calculate($target_id);
                } else {
                    log_message('error', 'Invoice ' . $target_id . 'could not be sent. Please review your Email settings.');
                }
            }
        }

        log_message('debug', '[Recurring Invoices] ' . count($invoices_recurring) . ' recurring invoices processed');
    }
}
