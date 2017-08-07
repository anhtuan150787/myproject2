<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ForgetModel extends MasterModel implements InputFilterAwareInterface
{
    protected $inputFilter;

    public function __construct($services)
    {
        $this->tableName = 'users';

        parent::__construct($services);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'users_email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'options' => [
                            'messages' => [
                                \Zend\Validator\NotEmpty::IS_EMPTY => 'Vui lòng nhập Email',
                            ]
                        ],
                        'break_chain_on_failure' => true,
                    ],
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'messages' => [
                                \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Email không đúng định dạng',
                            ]
                        ]
                    ],
                ],
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}