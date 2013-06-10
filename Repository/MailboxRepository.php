<?php

namespace Lasso\VmailBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lasso\VmailBundle\Entity\Email;
use Lasso\VmailBundle\Entity\Mailbox;
use Lasso\VmailBundle\Exception\VmailException;

/**
 * Class MailboxRepository
 * @package Lasso\VmailBundle\Repository
 */
class MailboxRepository extends EntityRepository
{

    /**
     * @param $username
     *
     * @return Mailbox
     */
    public function getMailbox($username)
    {
        $this->validateUserName($username);

        $mailbox = new Mailbox();
        $mailbox->setUsername($username);

        return $mailbox;
    }

    /**
     * @param $username
     *
     * @throws \Lasso\VmailBundle\Exception\VmailException
     */
    private function validateUserName($username)
    {
        if (strlen($username) <= 3) {
            throw VmailException::invalidUsername($username);
        }
    }

    /**
     * @return Mailbox[]
     */
    public function getMailboxes($search = '', $limit = false, $offset = false, $sort = [])
    {
        $qb = $this->createQueryBuilder('m');
        if ($search) {
            $qb->where("m.username LIKE :username");
            $qb->setParameter('username', "%$search%");
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        if (!empty($sort->property)) {
            $qb->orderBy('m.' . $sort->property, $sort->direction);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return int count
     */
    public function getMailboxCount($search = '')
    {
        $mailboxes = $this->getMailboxes($search);

        return count($mailboxes);
    }
}
