<?php
/**
 * @file     class_hash.php
 * @folder   /libs
 * @version  0.1
 * @author   Sweil
 *
 * this class provides access and datahandling for confirment hashes
 */
require('class_hash.php');

class HashMapper
{
    // Klassen-Variablen
    private $sql;

    // Der Konstruktur
    public function  __construct() {
        global $FD;
        $this->sql = $FD->sql();
    }

    // Hash laden
    private function getById($id) {
        $data = $this->sql->getById('hashes', '*', $id);

        if (!empty($data)) {
			$hash = new Hash($data);
            if ($this->checkDeleteTime($hash))
                return $hash;
		}

        Throw new Exception('Hash not found.');
    }

    public function getByHash($hash) {
        $data = $this->sql->getById('hashes', '*', $hash, 'hash');

        if (!empty($data)) {
			$hash = new Hash($data);
            if ($this->checkDeleteTime($hash))
                return $hash;
		}

        Throw new Exception('Hash not found.');
    }

    public function checkDeleteTime($hash) {
        global $FD;

        if ($hash->getDeleteTime() < $FD->cfg('env', 'date')) {
            $this->delete($hash);
            return false;
        }
        return true;
    }


    // save to DB
    public function save($hash) {
        $data = array(
            'hash' => $hash->getHash(),
            'type' => $hash->getType(),
            'typeId' => $hash->getTypeId()
        );

        // no ID?
        if (null === ($data['id'] = $hash->getId()))
            unset($data['id']);

        // save to db
        try {
            return $this->getById($this->sql->save('hashes', $data));
        } catch (Exception $e) {
            Throw $e;
        }
    }

    // save to DB
    public function delete($hash) {
        $this->sql->deleteById('hashes', $hash->getId());
    }


    // Create new hash By Payment
    public function createForNewPassword($userid) {
        $hash = new Hash();
        $hash->setHash($this->calculateHash());
        $hash->setType('newpassword');
        $hash->setTypeId($userid);
        $hash->setDeleteTime(time()+2*24*60*60);
        return $this->save($hash);
    }


    // Calculate Hash
    private function calculateHash() {
        $LENGHT = 40;
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789';
        $hash = '';
        $real_strlen = strlen ( $charset ) - 1;
        mt_srand ( (double)microtime () * 1001000 );

        while ( strlen ( $hash ) < $LENGHT ) {
            $hash .= $charset[mt_rand ( 0,$real_strlen ) ];
        }
        return $hash;
    }
}
?>
