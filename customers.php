<?php
require __DIR__ . '/app/bootstrap.php';
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
$bootstrap->run($app);

$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

$customersModel = $objectManager->create('\TemplateMonster\Ebriza\Model\Customers');
$customersModel->syncCustomers();