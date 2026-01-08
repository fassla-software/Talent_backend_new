'use strict';
module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.addColumn('plumber_categories', 'product_flag', {
      type: Sequelize.BOOLEAN,
      defaultValue: true,
      allowNull: false,
    });
  },

  down: async queryInterface => {
    await queryInterface.removeColumn('plumber_categories', 'product_flag');
  },
};
