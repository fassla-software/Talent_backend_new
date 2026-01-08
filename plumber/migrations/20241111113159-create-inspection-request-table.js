'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    // Ensure the 'users' table exists before creating 'inspection_requests'
    const tables = await queryInterface.sequelize.getQueryInterface().showAllTables();
    if (!tables.includes('users')) {
      throw new Error("'users' table does not exist. Ensure 'users' migration runs before 'inspection_requests'.");
    }

    await queryInterface.createTable('inspection_requests', {
      id: {
        type: Sequelize.INTEGER,
        primaryKey: true,
        autoIncrement: true,
        allowNull: false,
      },
      requestor_name: {
        type: Sequelize.STRING,
        allowNull: false,
      },
      requestor_phone: {
        type: Sequelize.STRING,
        allowNull: false,
      },
      nationality_id: {
        type: Sequelize.STRING,
        allowNull: false,
      },
      city: {
        type: Sequelize.STRING,
        allowNull: false,
      },
      area: {
        type: Sequelize.STRING,
        allowNull: false,
      },
      address: {
        type: Sequelize.STRING,
        allowNull: false,
      },
      seller_name: {
        type: Sequelize.STRING,
        allowNull: false,
      },
      seller_phone: {
        type: Sequelize.STRING,
        allowNull: false,
      },
      certificate_id: {
        type: Sequelize.STRING,
        allowNull: false,
      },
      inspection_date: {
        type: Sequelize.DATE,
        allowNull: false,
      },
      inspector_id: {
        type: Sequelize.BIGINT(20).UNSIGNED,
        allowNull: true,
        references: {
          model: 'users',
          key: 'id',
        },
        onDelete: 'CASCADE',
        onUpdate: 'CASCADE',
      },
      description: {
        type: Sequelize.STRING,
        allowNull: true,
      },
      images: {
        type: Sequelize.JSON,
        allowNull: true,
      },
      status: {
        type: Sequelize.STRING,
        allowNull: false,
        defaultValue: 'PENDING',
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
    await queryInterface.dropTable('inspection_requests');
  },
};
