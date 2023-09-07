<?php

namespace Opencart\Catalog\Model\Extension\SmsAlert\Service;

class Order extends \Opencart\System\Engine\Model
{
    /**
     * @param $statusId
     *
     * @return false|mixed
     */
    public function getStatusName($statusId)
    {
        $sql = sprintf(
            "SELECT os.name FROM %sorder_status os WHERE os.order_status_id = %s AND os.language_id = 1",
            DB_PREFIX,
            $statusId
        );
        $order_status = $this->db->query($sql);

        if ($order_status->num_rows) {
            return $order_status->row['name'];
        }
        return false;
    }
}