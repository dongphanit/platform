<?php


namespace Oro\Bundle\OrganizationBundle\Api\Processor;

use Oro\Bundle\ApiBundle\Processor\CustomizeFormData\CustomizeFormDataContext;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
/**
 * Assigns an entity to the current customer.
 */
class ComputeOrganizations implements ProcessorInterface
{

    /** @var TokenAccessorInterface */
    private $tokenAccessor;


    /**
     * @param TokenAccessorInterface    $tokenAccessor
     */
    public function __construct(
        TokenAccessorInterface $tokenAccessor
    ) {
        $this->tokenAccessor = $tokenAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        $out = array();
        /** @var CustomizeFormDataContext $context */
        $user = $this->tokenAccessor->getUser();
        $linkCustomersOrganizations = $context->getResultFieldName('linkCustomersOrganizations');
        // $this-> debug_to_console($linkCustomersOrganizations);
        if (true){
            $data = $context->getData();
            $str = json_encode($data);
            $dataJson = json_decode($str);
            $index = 0;
            foreach ($data as $key => $item) {
                $itemJson = $dataJson[$index];
                $links = $itemJson->linkCustomersOrganizations;
                // $this-> debug_to_console(json_encode($links));
                if (sizeof($links) > 0){
                    if (property_exists($links[0], 'customer')){
                        foreach ($links as &$value) {
                            // $str = json_encode($value);
                            // $this ->debug_to_console($str);
                            $customer = $value->customer;
                            if ($customer != NULL){
                                // $this ->debug_to_console($user->getCustomer()->getId());
                                if ($customer->id == $user->getCustomer()->getId() and $value->status==1){
                                    array_push($out, $item);
                                }
                            }
                            
                        }
                    }
                    else{
                        return;
                    }
                }
                $index = $index + 1;
            }
            if (sizeof($out) != sizeof($dataJson)){
                $context->setData($out);
            }
        }
     

    }

    function debug_to_console($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);
    
        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }

}
