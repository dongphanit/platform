<?php

namespace Oro\Bundle\ApiBundle\Util;

use Doctrine\ORM\Query;
use Oro\Bundle\SecurityBundle\AccessRule\AclAccessRule;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Component\DoctrineUtils\ORM\QueryHintResolverInterface;
use Oro\Component\EntitySerializer\EntityConfig;
use Oro\Component\EntitySerializer\QueryResolver;

/**
 * This query resolver modifies API queries in order to protect data
 * that can be retrieved via these queries.
 */
class AclProtectedQueryResolver extends QueryResolver
{
    public const SKIP_ACL_FOR_ROOT_ENTITY = 'skip_acl_for_root_entity';

    /** @var AclHelper */
    private $aclHelper;

    /**
     * @param QueryHintResolverInterface $queryHintResolver
     * @param AclHelper                  $aclHelper
     */
    public function __construct(QueryHintResolverInterface $queryHintResolver, AclHelper $aclHelper)
    {
        parent::__construct($queryHintResolver);
        $this->aclHelper = $aclHelper;
    }

    function debug_to_console($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);
    
        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }

    /**
     * {@inheritdoc}
     */
    public function resolveQuery(Query $query, EntityConfig $config)
    {
        // $this -> debug_to_console(json_encode($config));
        $options = [AclAccessRule::CHECK_OWNER => true];
        $skipRootEntity = (bool)$config->get(self::SKIP_ACL_FOR_ROOT_ENTITY);
        if (array_key_exists(self::SKIP_ACL_FOR_ROOT_ENTITY, $config->getFields())){
            $skipRootEntity = true;
        }
        
        if ($skipRootEntity) {
            $options[AclHelper::CHECK_ROOT_ENTITY] = false;
        }
        else{
            // $this->aclHelper->apply($query, 'VIEW', $options);
        }
    
        parent::resolveQuery($query, $config);
    }
}
