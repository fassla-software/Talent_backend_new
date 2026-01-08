'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    // Create inspection_requests_items table
    await queryInterface.createTable('inspection_requests_items', {
      id: {
        type: Sequelize.INTEGER,
        primaryKey: true,
        autoIncrement: true,
        allowNull: false,
      },
      inspection_request_id: {
        type: Sequelize.INTEGER,
        allowNull: true,
        references: {
          model: 'inspection_requests',
          key: 'id',
        },
        onDelete: 'CASCADE',
        onUpdate: 'CASCADE',
      },
      subcategory_id: {
        type: Sequelize.INTEGER,
        allowNull: true,
        references: {
          model: 'plumber_categories',
          key: 'id',
        },
        onDelete: 'SET NULL',
        onUpdate: 'CASCADE',
      },
      count: {
        type: Sequelize.INTEGER,
        allowNull: false,
        defaultValue: 0,
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
  },

  async down(queryInterface) {
    // Drop the inspection_requests_items table
    await queryInterface.dropTable('inspection_requests_items');
  },
};
