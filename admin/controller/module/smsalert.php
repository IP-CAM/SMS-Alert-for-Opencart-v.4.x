<?php

namespace Opencart\Admin\Controller\Extension\SmsAlert\Module;

/**
 * Class SmsAlert
 */
class SmsAlert extends \Opencart\System\Engine\Controller
{

    private $error       = [];
    private $code        = ['smsalert_test', 'smsalert'];
    public  $testResult  = false;
    private $fields_test = [
        "smsalert_test_phone_number" => [
            "label"    => "Phone Number",
            "type"     => "isPhoneNumber",
            "value"    => "",
            "validate" => true,
        ],
    ];
    private $fields               = [
        "smsalert_username" => ["label" => "Username", "type" => "isEmpty", "value" => "", "validate" => true],
        "smsalert_apiKey"   => ["label" => "API Key", "type" => "isEmpty", "value" => "", "validate" => true],
        "smsalert_active" => ["value" => ""],

        "smsalert_canceled_active"  => ["value" => ""],
        "smsalert_canceled_message" => ["value" => ""],

        "smsalert_canceled_reversal_active"  => ["value" => ""],
        "smsalert_canceled_reversal_message" => ["value" => ""],

        "smsalert_chargeback_active"  => ["value" => ""],
        "smsalert_chargeback_message" => ["value" => ""],

        "smsalert_complete_active"  => ["value" => ""],
        "smsalert_complete_message" => ["value" => ""],

        "smsalert_denied_active"  => ["value" => ""],
        "smsalert_denied_message" => ["value" => ""],

        "smsalert_refunded_active"  => ["value" => ""],
        "smsalert_refunded_message" => ["value" => ""],

        "smsalert_expired_active"  => ["value" => ""],
        "smsalert_expired_message" => ["value" => ""],

        "smsalert_failed_active"  => ["value" => ""],
        "smsalert_failed_message" => ["value" => ""],

        "smsalert_pending_active"  => ["value" => ""],
        "smsalert_pending_message" => ["value" => ""],

        "smsalert_processed_active"  => ["value" => ""],
        "smsalert_processed_message" => ["value" => ""],

        "smsalert_processing_active"  => ["value" => ""],
        "smsalert_processing_message" => ["value" => ""],

        "smsalert_reversed_active"  => ["value" => ""],
        "smsalert_reversed_message" => ["value" => ""],

        "smsalert_shipped_active"  => ["value" => ""],
        "smsalert_shipped_message" => ["value" => ""],

        "smsalert_voided_active"  => ["value" => ""],
        "smsalert_voided_message" => ["value" => ""],
    ];

    public function index(): void
    {
        if (!$this->isModuleEnabled()) {
            $this->response->redirect(
                $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
            );
            exit;
        }

        $this->load->language('extension/smsalert/module/smsalert');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addStyle('../extension/smsalert/admin/view/stylesheet/smsalert.css');

        $this->load->model('setting/setting');
        $this->load->model('setting/module');
        $this->load->model('design/layout');
        $this->load->model('extension/smsalert/service/validator');
        $this->load->model('extension/smsalert/service/helper');

        $this->submitted();
        $this->loadFieldsToData($data);

        $data['error_warning'] = $this->error;

        $data['smsalert_logo'] = '/extension/smsalert/admin/view/image/smsalert/logo.png';

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit']     = $this->language->get('text_edit');

        $data['btn_test_text']        = $this->language->get('btn_test_text');
        $data['btn_test_placeholder'] = $this->language->get('btn_test_placeholder');
        $data['btn_test_description'] = $this->language->get('btn_test_description');
        $data['btn_test_send']        = $this->language->get('btn_test_send');

        $data['btn_apiKey_text']        = $this->language->get('btn_apiKey_text');
        $data['btn_apiKey_placeholder'] = $this->language->get('btn_apiKey_placeholder');
        $data['btn_apiKey_description'] = $this->language->get('btn_apiKey_description');

        $data['btn_username_text']        = $this->language->get('btn_username_text');
        $data['btn_username_placeholder'] = $this->language->get('btn_username_placeholder');
        $data['btn_username_description'] = $this->language->get('btn_username_description');

        $data['btn_token_save_all'] = $this->language->get('btn_token_save_all');

        $data['btn_status_order_description'] = $this->language->get('btn_status_order_description');

        $data['btn_status_order_canceled']          = $this->language->get('btn_status_order_canceled');
        $data['btn_status_order_canceled_reversal'] = $this->language->get('btn_status_order_canceled_reversal');
        $data['btn_status_order_chargebackd']       = $this->language->get('btn_status_order_chargebackd');
        $data['btn_status_order_complete']          = $this->language->get('btn_status_order_complete');
        $data['btn_status_order_denied']            = $this->language->get('btn_status_order_denied');
        $data['btn_status_order_expired']           = $this->language->get('btn_status_order_expired');
        $data['btn_status_order_failed']            = $this->language->get('btn_status_order_failed');
        $data['btn_status_order_pending']           = $this->language->get('btn_status_order_pending');
        $data['btn_status_order_processed']         = $this->language->get('btn_status_order_processed');
        $data['btn_status_order_processing']        = $this->language->get('btn_status_order_processing');
        $data['btn_status_order_refunded']          = $this->language->get('btn_status_order_refunded');
        $data['btn_status_order_reversed']          = $this->language->get('btn_status_order_reversed');
        $data['btn_status_order_shipped']           = $this->language->get('btn_status_order_shipped');
        $data['btn_status_order_voided']            = $this->language->get('btn_status_order_voided');

        $data['smsalert_test_result'] = $this->testResult;

        # common template
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/smsalert/settings/smsalert', $data));
    }

    public function isModuleEnabled()
    {
        $sql    = sprintf("SELECT * FROM %sextension WHERE code = 'smsalert'", DB_PREFIX);
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            return true;
        }

        return false;
    }

    public function submitted()
    {
        if (!empty($_POST)) {
            if (!empty($_POST['smsalert_test'])) {
                $this->validateFields();
                if (empty($_POST['smsalert_apiKey'])) {
                    $this->error[] = ["error" => "Field api key is required for testing."];
                }

                if (empty($_POST['smsalert_username'])) {
                    $this->error[] = ["error" => "Username is required for testing."];
                }

                if (empty($this->error)) {
                    $this->saveFieldsToDB();
                    $fields = $this->getFieldsValue();

                    $message = 'Test opencart SMS message with SMSAlert.mobi';
                    $result  = $this->model_extension_smsalert_service_helper->sendTestSMS(
                        $fields['smsalert_test_phone_number']['value'],
                        $message
                    );

                    if ($result) {
                        $this->testResult = true;
                    }
                }
            } else {

                $this->validateFields();
                if (empty($this->error)) {
                    $this->saveFieldsToDB();
                }
            }

            return true;
        }

        return false;
    }

    public function loadFieldsToData(&$data)
    {
        foreach ($this->fields as $key => $value) {
            $data[$key] = $this->model_setting_setting->getValue($key);
        }

        foreach ($this->fields_test as $key => $value) {
            $data[$key] = $this->model_setting_setting->getValue($key);
        }
    }

    public function saveFieldsToDB()
    {
        $fields = $this->getPostFiles();

        foreach (array_keys($fields) as $key) {
            if (isset($_POST[$key])) {
                $fields[$key] = $_POST[$key];
            } else {
                $fields[$key] = "";
            }
        }

        if (empty($_POST['smsalert_test'])) {
            $module_fields = [];
            if ($fields['smsalert_active']) {
                $module_fields['module_smsalert_status'] = 'true';
            } else {
                $module_fields['module_smsalert_status'] = 'false';
            }
            $this->model_setting_setting->editSetting("module_smsalert", $module_fields);
        }

        $this->model_setting_setting->editSetting($this->getCode(), $fields);
    }

    public function validateFields()
    {
        $fields = $this->getPostFiles();

        foreach ($fields as $key => $value) {
            if (isset($value['validate'])) {
                $result = call_user_func_array(
                    [$this->model_extension_smsalert_service_validator, $value['type']],
                    [$_POST[$key]]
                );
                if (!$result) {
                    $this->error[] = ["error" => "Field ".$value['label']." is required for testing."];
                }
            }
        }
    }

    public function getFieldsValue()
    {
        $fields = $this->getPostFiles();

        foreach ($fields as $key => $value) {
            $fields[$key]["value"] = $this->model_setting_setting->getValue($key);
        }

        return $fields;
    }

    public function getPostFiles()
    {
        return (!empty($_POST['smsalert_test']) ? $this->fields_test : $this->fields);
    }

    public function getCode()
    {
        return (!empty($_POST['smsalert_test']) ? $this->code[0] : $this->code[1]);
    }


    public function install()
    {
        $this->load->model('setting/event');
        $this->model_setting_event->addEvent([
            'code' => 'smsalert_order_event',
            'trigger' => 'catalog/model/checkout/order/addHistory/before',
            'action' => 'extension/smsalert/module/smsalert.statusChange',
            'description' => 'SMSAlert order status event',
            'sort_order' => 1,
            'status' => true
        ]);
    }

    public function uninstall()
    {
        $this->model_setting_event->deleteEventByCode('smsalert_order_event');
    }
}