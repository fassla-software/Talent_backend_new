'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    // Ensure the 'inspection_requests' and 'report_visits' tables exist
    const tables = await queryInterface.sequelize.getQueryInterface().showAllTables();
    if (!tables.includes('inspection_requests')) {
      throw new Error("'inspection_requests' table does not exist. Ensure 'inspection_requests' migration runs first.");
    }
    if (!tables.includes('report_visits')) {
      throw new Error("'report_visits' table does not exist. Ensure 'report_visits' migration runs first.");
    }

    await queryInterface.createTable('inspection_visits', {
      id: {
        type: Sequelize.INTEGER,
        primaryKey: true,
        autoIncrement: true,
        allowNull: false,
      },
      inspection_request_id: {
        type: Sequelize.INTEGER,
        allowNull: false,
        references: {
          model: 'inspection_requests',
          key: 'id',
        },
        onDelete: 'CASCADE',
        onUpdate: 'CASCADE',
      },
      inspector_id: {
        type: Sequelize.BIGINT(20).UNSIGNED,
        allowNull: false,
        references: {
          model: 'users',
          key: 'id',
        },
        onDelete: 'CASCADE',
        onUpdate: 'CASCADE',
      },
      report_id: {
        type: Sequelize.INTEGER,
        allowNull: true,
        references: {
          model: 'report_visits',
          key: 'id',
        },
        onDelete: 'SET NULL',
        onUpdate: 'CASCADE',
      },
      check_in_at: {
        type: Sequelize.DATE,
        allowNull: true,
      },
      check_in_latitude: {
        type: Sequelize.DECIMAL(10, 8),
        allowNull: true,
      },
      check_in_longitude: {
        type: Sequelize.DECIMAL(11, 8),
        allowNull: true,
      },
      check_out_at: {
        type: Sequelize.DATE,
        allowNull: true,
      },
      check_out_latitude: {
        type: Sequelize.DECIMAL(10, 8),
        allowNull: true,
      },
      check_out_longitude: {
        type: Sequelize.DECIMAL(11, 8),
        allowNull: true,
      },
      created_at: {
        type: Sequelize.DATE,
        allowNull: false,
        defaultValue: Sequelize.NOW,
      },
      updated_at: {
        type: Sequelize.DATE,
        allowNull: false,
        defaultValue: Sequelize.NOW,
      },
    });

    // Add indexes
    await queryInterface.addIndex('inspection_visits', ['inspection_request_id']);
    await queryInterface.addIndex('inspection_visits', ['inspector_id']);
    await queryInterface.addIndex('inspection_visits', ['report_id']);
  },

  async down(queryInterface) {
    await queryInterface.dropTable('inspection_visits');
  },
};

