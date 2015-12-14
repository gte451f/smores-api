<?php
namespace PhalconRest\Controllers;

/**
 * extend from account specific controller
 *
 * @author jjenkins
 *        
 */
class AccountController extends \PhalconRest\Libraries\API\SecureAccountController
{

    public function getBillable()
    {
        $db = $this->di->get(db);
        
        $search_result = [];
        // $search_result = $this->entity->find();
        
        $sql = "SELECT a.id, 
c.total AS charge_total, c.created_on AS charge_created_on, c.quantity AS charge_count,
p.total AS payment_total, p.created_on AS payment_created_on, p.quantity AS payment_count
 FROM accounts AS a
 JOIN (
 SELECT account_id, SUM(amount) AS total, MAX(created_on) AS created_on, COUNT(*) AS quantity
FROM charges AS c
GROUP BY c.account_id) AS c ON a.id = c.account_id
JOIN (
SELECT account_id, SUM(amount) AS total, MAX(created_on) AS created_on, COUNT(*) AS quantity
FROM payments
GROUP BY account_id
) AS p ON a.id = p.account_id;";
        
        $result_set = $db->query($sql);
        $result_set->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $result_set = $result_set->fetchAll($result_set);
        
        return $this->respond($result_set);
    }
}