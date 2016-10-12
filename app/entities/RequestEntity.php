<?php
namespace PhalconRest\Entities;

use \PhalconRest\Exception\ValidationException;

class RequestEntity extends \PhalconRest\Libraries\API\Entity
{

    /**
     * the value assigned to a request that is attending camp
     * this is used to determine if fees should be applied and if the request
     * occupy one of the open spaces in a cabin's capcity
     *
     * @var string
     */
    const CONFIRMED = 'Confirmed';

    /**
     * the value assigned to a request that is provisionally attending camp
     * this status will cause the request to count against a cabins capacity
     * fees are not applied yet
     *
     * @var string
     */
    const PENDING = 'Pending';

    /**
     * if a request has it's status modified, inspect the related fees and make sure they are correct
     *
     * (non-PHPdoc)
     *
     * @see \PhalconRest\API\Entity::afterSave()
     */
    public function afterSave($object, $id)
    {
        $request = $this->model->findFirst($id);

        $this->toggleFees($request);
    }

    /**
     * for a given request, will toggle fees on/off depeneding on the request status
     * will take care not to duplicate fees that are already applied
     *
     * @param \PhalconRest\Models\Request $request
     */
    private function toggleFees(\PhalconRest\Models\Requests $request)
    {

        // are we on or off?
        if ($request->submit_status == self::CONFIRMED) {

            $fees = $this->getFees($request);

            foreach ($fees as $fee) {
                $charge = new \PhalconRest\Models\Charges();
                $charge->registration_id = $request->registration_id;
                $charge->request_id = $request->id;
                $charge->name = $fee['name'];
                $charge->amount = $fee['amount'];
                $attendee = $request->Registrations->Attendees;
                $charge->account_id = $attendee->account_id;

                if (!$charge->create()) {
                    throw new ValidationException("Internal error saving a request", array(
                        'code' => '45623457456987986',
                        'dev' => 'Error while processing RequestEntity->toggleFee().  Could not create Charge record.'
                    ), $charge->getMessages());
                }
            }
        } else {
            // remove fees
            foreach (\PhalconRest\Models\Charges::find("request_id=$request->id") as $charge) {
                if (!$charge->delete()) {
                    throw new ValidationException("Internal error clearing out charges", array(
                        'code' => '234546678345',
                        'dev' => 'Error while attempting to delete a charge for a request that is no longer CONFIRMED.'
                    ), $charge->getMessages());
                }
            }
        }
    }

    /**
     * for a given request model, load up any fees that should be applied when CONFIRMED
     *
     * @param \PhalconRest\Models\Requests $request
     * @return array
     */
    final public function getFees(\PhalconRest\Models\Requests $request)
    {
        // apply fees
        $event = $request->Events;
        $program = $event->Programs;

        $fees = array();

        if ($event->fee > 0) {
            $fees[] = array(
                'amount' => $event->fee,
                'name' => $event->fee_description
            );
        }

        if ($program->fee > 0) {
            $fees[] = array(
                'amount' => $program->fee,
                'name' => $program->name . ' Fee'
            );
        }

        return $fees;
    }

    /**
     * custom implimentation of account filter
     * must filter through related registration - attendee records
     *
     * {@inheritDoc}
     *
     * @see \PhalconRest\Libraries\API\Entity::applyAccountFilter()
     */
    public function applyAccountFilter($query)
    {
        // load current account
        $currentUser = $this->getDI()
            ->get('auth')
            ->getProfile();
        // add custom filter
        $query->andWhere("Attendees.account_id = $currentUser->accountId");

        // only add needed join if it isn't already in place
        $applyJoin = true;
        foreach ($this->activeRelations as $alias => $relation) {
            if ($alias == 'Registrations') {
                $applyJoin = false;
                break;
            }
        }
        if ($applyJoin) {
            $query->join('PhalconRest\Models\Registrations',
                "Registrations.id = PhalconRest\\Models\\Requests.registration_id", "Registrations");
        }

        // use registration to reach attendees for the filter
        $query->join('PhalconRest\Models\Attendees', "Registrations.attendee_id = Attendees.user_id", "Attendees");
        return $query;
    }
}