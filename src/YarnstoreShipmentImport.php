<?php declare(strict_types=1);

namespace Yarnstore\ShipmentImport;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin;

class YarnstoreShipmentImport extends Plugin
{
    /**
     * @param \Shopware\Core\Framework\Plugin\Context\InstallContext $context
     * 
     * @return void
     */
    public function install(InstallContext $context):void
    {
        parent::install($context);
    }

    /**
     * @param \Shopware\Core\Framework\Plugin\Context\UninstallContext $context
     * 
     * @return void
     */
    public function uninstall(UninstallContext $context):void
    {
        if ($context->keepUserData()) {
            return;
        }
    }
}