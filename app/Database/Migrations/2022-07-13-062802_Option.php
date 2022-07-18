<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Option extends Migration
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

		$this->forge->addField($fields);
        
		$this->forge->addPrimaryKey('id');
        $this->forge->addKey(["key"]);

		$this->forge->createTable("option", true, array('ENGINE' => 'InnoDB'));
    }

    public function down()
    {
        $this->forge->dropTable('option');
    }
}
