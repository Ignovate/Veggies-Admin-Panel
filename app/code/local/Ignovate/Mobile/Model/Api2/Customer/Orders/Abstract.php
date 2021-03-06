<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 08/11/17
 * Time: 12:41 PM
 */
class Ignovate_Mobile_Model_Api2_Customer_Orders_Abstract extends Ignovate_Api2_Model_Resource
{
    /**
     * Load customer by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Customer_Model_Customer
     */
    protected function _loadCustomerById($id)
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')->load($id);
        if (!$customer->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $customer;
    }

    public function buildCustomerOrderObj($customer, $storeId)
    {
        $debug = true;
        // Get Customer Log
        $readAdapter = Mage::getSingleton('core/resource')
            ->getConnection('core_read');

        $collectionSelect = $readAdapter->select()
            ->from(
                array('order' => 'sales_flat_order_grid'),
                array(
                    'order_id' => 'order.entity_id',
                    'increment_id' => 'order.increment_id',
                    'name'  => 'order.shipping_name',
                    'total' => 'order.grand_total',
                    'ordered_date' => 'order.created_at'
                )
            );

        $collectionSelect->joinLeft(
                array('statuses' => 'sales_order_status'),
                'statuses.status = order.status',
                array('status' => 'statuses.label')
            )
            ->joinLeft(
                array('store' => 'core_store'),
                'store.store_id = order.store_id',
                array('area' => 'store.name')
            );

        $collectionSelect
            ->where(
                'order.customer_id = ?', $customer->getId()
            )
            ->order(
                'order.created_at DESC'
            );

        $indexData = $readAdapter->query($collectionSelect)->fetchAll();

        foreach ($indexData as $key => $each) {
            $date = date('Y-m-d H:i:s', strtotime($each['ordered_date']. ' + 330 mins'));
            $indexData[$key]['ordered_date'] = $date;
        }

        return $indexData;
    }
}