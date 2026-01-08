'use strict';

module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.createTable('report_dropdown_options', {
      id: {
        type: Sequelize.INTEGER,
        primaryKey: true,
        autoIncrement: true,
        allowNull: false,
      },
      dropdown_type: {
        type: Sequelize.STRING(50),
        allowNull: false,
      },
      key: {
        type: Sequelize.STRING(100),
        allowNull: false,
      },
      value_en: {
        type: Sequelize.STRING(255),
        allowNull: false,
      },
      value_ar: {
        type: Sequelize.STRING(255),
        allowNull: true,
      },
      is_static: {
        type: Sequelize.BOOLEAN,
        defaultValue: false,
      },
      created_at: {
        type: Sequelize.DATE,
        allowNull: false,
      },
      updated_at: {
        type: Sequelize.DATE,
        allowNull: false,
      },
    });

    await queryInterface.addIndex('report_dropdown_options', ['dropdown_type']);
  },

  down: async (queryInterface, Sequelize) => {
    await queryInterface.dropTable('report_dropdown_options');
  }
};