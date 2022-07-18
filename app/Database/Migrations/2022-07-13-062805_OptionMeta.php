<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OptionMeta extends Migration
{
    public function up()
    {
        //option_meta
        $fields = array(
			'id' => array(
				'type' => 'INT',
				'null' => false,
				'auto_increment' => true,
			),
			'option' => array(
				'type' => 'INT',
			),
			'key' => array(
				'type' => 'VARCHAR',
				'constraint'     => 50,
			),
			'value' => array(
				'type' => 'TEXT',
			),
			'created_at' => array(
				'type'           => 'INT',
				'constraint'     => 9,
			),
			'updated_at' => array(
				'type'           => 'INT',
				'constraint'     => 9,
			),
			'deleted_at' => array(
				'type'           => 'INT',
				'constraint'     => 9,
			),
		);

		$this->forge->addKey('id', true);
		$this->forge->addField($fields);
        
        $this->forge->addForeignKey("option", "option", "id", "CASCADE", "CASCADE");
        $this->forge->addKey(["key", "option"]);

		$this->forge->createTable("option_meta", true, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->forge->dropTable('option_meta');
    }
}
