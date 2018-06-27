<?php

$this->startSetup();


$this->getConnection()->addColumn(
    $this->getTable('sales/order'),
    'is_app',
    'INT(10)'
);

$this->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'is_app',
    'INT(10)'
);

$this->endSetup();