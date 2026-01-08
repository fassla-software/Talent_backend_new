'use strict';

module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.addColumn('plumbers', 'withdraw_money', {
      type: Sequelize.INTEGER,
      allowNull: false,
      default: 0,
    });
  },

  down: async queryInterface => {
    await queryInterface.removeColumn('plumbers', 'withdraw_money');
  },
};
