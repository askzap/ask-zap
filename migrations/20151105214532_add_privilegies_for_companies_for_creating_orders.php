<?php

use Phinx\Migration\AbstractMigration;

class AddPrivilegiesForCompaniesForCreatingOrders extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("REPLACE INTO cscart_privileges (privilege, is_default, section_id) VALUES ('add_order_management', 'Y', 'orders');");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}