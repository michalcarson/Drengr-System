<?php

namespace Drengr\Repository;

use Drengr\Exception\ModelNotFoundException;
use Drengr\Framework\Repository;
use WP_User;

class MemberRepository extends Repository
{
    protected $table = 'member';

    /**
     * @param object|WP_User $user
     * @return array|object|void|null
     */
    public function findByWpUserOrFail(object $user)
    {
        $member = $this->findBy([
            'wp_user_id' => $user->ID
        ]);

        if (empty($member)) {
            throw new ModelNotFoundException();
        }

        return $member;
    }

    public function getEmail($id)
    {
        $table1 = $this->database->getPrefix() . 'member_email';
        $table2 = $this->database->getPrefix() . 'email_type';
        return $this->database->query(
            sprintf(
                "select a.*, b.name as type
                    from %s a, %s b
                    where a.member_id=%d 
                    and a.email_type_id=b.id",
                $table1,
                $table2,
                $id
            )
        );
    }

    public function getPhone($id)
    {
        $table1 = $this->database->getPrefix() . 'member_phone';
        $table2 = $this->database->getPrefix() . 'phone_type';
        return $this->database->query(
            sprintf(
                "select a.*, b.name as type
                    from %s a, %s b
                    where a.member_id=%d 
                    and a.phone_type_id=b.id",
                $table1,
                $table2,
                $id
            )
        );
    }

    public function getRanks($id)
    {
        $table1 = $this->database->getPrefix() . 'member_rank';
        $table2 = $this->database->getPrefix() . 'rank';
        return $this->database->query(
            sprintf(
                "select a.*, b.name as rank
                    from %s a, %s b
                    where a.member_id=%d 
                    and a.rank_id=b.id",
                $table1,
                $table2,
                $id
            )
        );
    }

    public function getOffice($id)
    {
        $table1 = $this->database->getPrefix() . 'member_office';
        $table2 = $this->database->getPrefix() . 'office';
        $table3 = $this->database->getPrefix() . 'group';
        return $this->database->query(
            sprintf(
                "select a.*, b.name as office, c.name as groupName
                    from  %s b, %s a
                    left outer join %s c on a.group_id=c.id
                    where a.member_id=%d 
                    and a.office_id=b.id",
                $table2,
                $table1,
                $table3,
                $id
            )
        );
    }

    public function getCertifications($id)
    {
        $table1 = $this->database->getPrefix() . 'member_certification';
        $table2 = $this->database->getPrefix() . 'certification';
        return $this->database->query(
            sprintf(
                "select a.*, b.name 
                    from %s a, %s b
                    where a.member_id=%d 
                    and a.certification_id=b.id",
                $table1,
                $table2,
                $id
            )
        );
    }
}
