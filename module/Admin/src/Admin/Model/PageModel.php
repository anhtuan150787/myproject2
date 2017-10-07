<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Validator\NotEmpty;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PageModel extends MasterModel implements InputFilterAwareInterface
{
    protected $inputFilter;

    public function __construct($services)
    {
        $this->tableName = 'post'; //VIEW POST
        $this->primary  = 'post_id';

        $this->tableView = 'view_post';

        parent::__construct($services);
    }

    public function fetchPage($postType = 1, $page, $pageItemPerCount, $search)
    {
        /*
         * Select
         */
        $select = 'SELECT ' . $this->tableView . '.post_id, post_title, post_picture, post_type, post_view, post_date_updated, post_users_updated, post_status, users_fullname,
                  (SELECT GROUP_CONCAT(post_category_name) FROM post_category_detail LEFT JOIN post_category ON post_category.post_category_id=post_category_detail.post_category_id WHERE post_id = ' . $this->tableView . '.post_id) AS post_category_names';

        /*
         * From
         */
        $from = ' FROM ' . $this->tableView;

        /*
         * Where
         */
        $where = ' WHERE 1=1 AND post_type = ' . $postType;

        if (isset($search['post_title_search'])) {
            $where .= ' AND post_title like "%' . $search['post_title_search']  . '%"';
        }

        if ((isset($search['post_date_from_search']) && $search['post_date_from_search'] != null) && (isset($search['post_date_to_search']) && $search['post_date_to_search'] != null)) {

            $date_from_arr = explode('/', $search['post_date_from_search']);
            $date_from = $date_from_arr[2] . '-' . $date_from_arr['1'] . '-' . $date_from_arr['0'] . ' 00:00:00';

            $date_to_arr = explode('/', $search['post_date_to_search']);
            $date_to = $date_to_arr[2] . '-' . $date_to_arr['1'] . '-' . $date_to_arr['0'] . ' 23:59:59';

            $where .= ' AND (post_date_updated between "' . $date_from . '" AND "' . $date_to . '")';
        }

        if (isset($search['post_category_search'])) {

            $from .= ' LEFT JOIN post_category_detail ON post_category_detail.post_id=' . $this->tableView . '.post_id';

            $where .= ' AND post_category_detail.post_category_id=' . $search['post_category_search'];
        }

        /*
         * Limit page
         */
        $start = ($page - 1) * $pageItemPerCount;
        $end = $pageItemPerCount;
        $limit = ' LIMIT ' . $start . ',' . $end;
        $orderBy = ' ORDER BY ' . $this->tableView . '.post_date_updated DESC, ' . $this->tableView . '.post_id DESC';

        $sql = $select . $from . $where . $orderBy . $limit;

        /*
         * Result
         * Get in Cache
         */
        $paramsCache = ['name' => 'countFetchPage', 'params' => $this->tableName . '.' . $this->primary . '.' . $sql];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {
            $statement = $this->tableGateway->getAdapter()->query($sql);
            $result = $statement->execute();
            $resultArray = iterator_to_array($result);
            $this->cache->setNameSpace($this->tableName)->set($keyCache, $resultArray);
        } else {
            $resultArray = $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }


        /*
         * Paging
         */
        $paging = new Paginator(new ArrayAdapter($resultArray));
        $paging->setCurrentPageNumber($page);
        $paging->setItemCountPerPage($pageItemPerCount);

        /*
         * Total
         * Get in Cache
         */
        $sqlTotal = 'SELECT count(' . $this->tableView . '.post_id) as total ' . $from . $where;
        $paramsCache = ['name' => 'countFetchPage', 'params' => $this->tableName . '.' . $this->primary . '.' . $sqlTotal];
        $keyCache = $this->getKeyCache($paramsCache);

        if (!$this->cache->setNameSpace($this->tableName)->checkItem($keyCache)) {
            $statement = $this->tableGateway->getAdapter()->query($sqlTotal);
            $resultTotal = $statement->execute();
            $resultTotal = $resultTotal->current();
            $total = $resultTotal['total'];
            $this->cache->setNameSpace($this->tableName)->set($keyCache, $total);
        } else {
            $total = $this->cache->setNameSpace($this->tableName)->get($keyCache);
        }

        return ['paging' => $paging, 'data' => $resultArray, 'total' => $total];
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'post_title',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'Stringtrim'],
                ],
                'validators' => [
                    [
                        'name' => 'not_empty',
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Vui lòng nhập thông tin',
                            ]
                        ],
                        'break_chain_on_failure' => true,
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }


}