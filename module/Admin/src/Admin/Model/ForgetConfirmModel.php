<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

use Zend\Validator\NotEmpty;
use Zend\Validator\EmailAddress;

class ForgetConfirmModel extends MasterModel implements InputFilterAwareInterface
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
                'name' => 'users_password',
                'required' => true,
                'filters' => [
                    ['name' => 'Stringtrim'],
                ],
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Vui lòng nhập Mật khẩu',
                            ]
                        ],
                        'break_chain_on_failure' => true,
                    ],
                ],
            ));

            $inputFilter->add(array(
                'name' => 'users_password_confirm',
                'required' => true,
                'filters' => [
                    ['name' => 'Stringtrim'],
                ],
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Vui lòng xác nhận lại Mật khẩu',
                            ]
                        ],
                        'break_chain_on_failure' => true,
                    ],
                ],
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}