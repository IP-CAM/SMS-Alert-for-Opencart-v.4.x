<?php

namespace Opencart\Catalog\Controller\Extension\SmsAlert\Module;

class SmsAlert extends \Opencart\System\Engine\Controller
{

    public function statusChange(string &$route, array &$data)
    {
        $orderStatusId = $data[1];
        $orderId       = $data[0];

        $this->load->model('setting/setting');
        $this->load->model('checkout/order');
        $this->load->model('extension/smsalert/service/order');
        $this->load->model('extension/smsalert/service/helper');

        $order        = $this->model_checkout_order->getOrder($orderId);
        $isActive     = $this->model_setting_setting->getValue("smsalert_active");
        $statusName   = $this->model_extension_smsalert_service_order->getStatusName($orderStatusId);

        if ($this->isModuleEnabled() && !empty($isActive) && !empty($statusName)) {
            $statusName     = str_replace(" ", "_", $statusName);
            $statusActivate = $this->model_setting_setting->getValue(
                "smsalert_" . strtolower($statusName) . "_active"
            );
            $statusMessage  = $this->model_setting_setting->getValue(
                "smsalert_" . strtolower($statusName) . "_message"
            );

            if (!empty($statusActivate) && !empty($statusMessage)) {
                $replace = [
                    '{order_number}'       => $order['order_id'],
                    '{order_date}'         => $order['date_added'],
                    '{order_total}'        => round(
                            $order['total'] * $order['currency_value'],
                            2
                        ) . ' ' . $order['currency_code'],
                    '{billing_first_name}' => $order['payment_firstname'],
                    '{billing_last_name}'  => $order['payment_lastname'],
                    '{shipping_method}'    => $order['shipping_method']['name'],
                ];

                foreach ($replace as $key => $value) {
                    $statusMessage = str_replace($key, $value, $statusMessage);
                }

                try{
                    $this->model_extension_smsalert_service_helper->sendSms($order['telephone'], $statusMessage);
                } catch (Exception $e) {
                    //
                }

            }
        }
    }

    public function isModuleEnabled()
    {
        $sql    = "SELECT * FROM " . DB_PREFIX . "extension WHERE code = 'smsalert'";
        $result = $this->db->query($sql);
        if ($result->num_rows) {
            return true;
        }

        return false;
    }

}