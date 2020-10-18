<?php
    class WA {
        /** @var SQLite3 */
        private static $db;

        function __construct() {
            self::$db = new SQLite3(__DIR__."/databases/wa.db");
        }

        /**
         * @return Contact[]
         */
        static function getContacts() {
            $r = [];
            $sql = self::$db->query("SELECT con._id as id, con.jid as jid, con.status as status, con.status_timestamp as statusTimestamp, substr(con.jid, 0, instr(con.jid, '@')) as num, con.display_name as displayName FROM wa_contacts as con WHERE number IS NOT NULL AND con.is_whatsapp_user = 1 order by displayName");
            while($row = $sql->fetchArray(SQLITE3_ASSOC)) {
                $r[] = new Contact($row);
            };
            return $r;
        }

        /**
         * @param string $num
         * @return Contact
         */
        static function getContactByNum($num) {
            $num = preg_replace("/^\+/", "", $num);
            $num = preg_replace("/\s+/", "", $num);

            $sql = self::$db->query("SELECT con._id as id, con.jid as jid, con.status as status, con.status_timestamp as statusTimestamp, substr(con.jid, 0, instr(con.jid, '@')) as num, con.display_name as displayName FROM wa_contacts as con WHERE number IS NOT NULL AND con.is_whatsapp_user = 1 AND num = '$num'");
            if($row = $sql->fetchArray()) {
                return new Contact($row);
            }
            throw new Exception("Number not found");
        }

        /**
         * @param string $jid
         * @return Contact
         */
        static function getContactByJid($jid) {
            $sql = self::$db->query("SELECT con._id as id, con.jid as jid, con.status as status, con.status_timestamp as statusTimestamp, substr(con.jid, 0, instr(con.jid, '@')) as num, con.display_name as displayName FROM wa_contacts as con WHERE number IS NOT NULL AND con.is_whatsapp_user = 1 AND jid = '$jid'");
            if($row = $sql->fetchArray()) {
                return new Contact($row);
            }
            throw new Exception("Jid not found");
        }

        /**
         * @return SQLite3
         */
        static function getDB() {
            return self::$db;
        }

        /**
         * @return DateTime
         */
        static function getDate() {
            return (new DateTime())->setTimestamp(filemtime(__DIR__ . "/databases/wa.db"));
        }
    }

    class MsgStore {
        /** @var SQLite3 */
        private static $db;

        function __construct() {
            self::$db = new SQLite3(__DIR__ . "/databases/msgstore.db");
        }

        /**
         * @param string $jid
         * @return Message[]
         */
        static function getMessagesWithJid($jid) {
            $r = [];
            $sql = self::$db->query("SELECT msg.timestamp as timestamp, data, key_from_me as me FROM messages as msg WHERE key_remote_jid = '$jid' AND msg.status NOT IN (6) ORDER BY msg._id ASC");
            while($row = $sql->fetchArray()) {
                $r[] = new Message($row);
            }
            return $r;
        }

        /**
         * @return SQLite3
         */
        static  function getDB() {
            return self::$db;
        }

        /**
         * @return DateTime
         */
        static function getDate() {
            return (new DateTime())->setTimestamp(filemtime(__DIR__ . "/databases/msgstore.db"));
        }

    }

    class Contact {
        /** @var string */
        private $jid;
        /** @var string */
        private $status;
        /** @var DateTime */
        private $statusTimestamp;
        /** @var string */
        private $num;
        /** @var string */
        private $displayName;

        /** @var Message[] */
        private $messages;

        /**
         * @param array $row Corresponding row in wa.db:wa_contacts
         */
        function __construct($row) {
            $this->jid              = $row["jid"];
            $this->status           = $row["status"];
            $this->statusTimestamp  = new DateTime();
            $this->statusTimestamp  ->setTimestamp($row["statusTimestamp"] / 1000);
            $this->num              = $row["num"];
            $this->displayName      = $row["displayName"];

            $this->loadMessages();
        }

        private function loadMessages() {
            $this->messages = MsgStore::getMessagesWithJid($this->jid);
        }

        /**
         * @return string
         */
        function getJid() {
            return $this->jid;
        }
        /**
         * @return string
         */
        function getStatus() {
            return $this->status;
        }
        /**
         * @return DateTime
         */
        function getStatusTimestamp() {
            return $this->statusTimestamp;
        }
        /**
         * @return string
         */
        function getNum() {
            return $this->num;
        }
        /**
         * @return string
         */
        function getDisplayName() {
            return $this->displayName;
        }
        /**
         * @return Message[]
         */
        function getMessages() {
            return $this->messages;
        }


        function __toString() : string {
            return "[jid => {$this->jid}, status => {$this->status}, num => {$this->num}, display_name => {$this->displayName}]";
        }
    }

    class Message {
        /** @var DateTime */
        private $timestamp;
        /** @var boolean */
        private $me;

        function __construct($row) {
            $this->timestamp    = new DateTime();
            $this->timestamp    ->setTimestamp($row["timestamp"]/1000);
            $this->me           = (boolean) $row["me"];
        }

        /**
         * @return DateTime
         */
        function getTimestamp() {
            return $this->timestamp;
        }

        /**
         * @return boolean
         */
        function isMe() {
            return $this->me;
        }
    }

    new WA();
    new MsgStore();
?>