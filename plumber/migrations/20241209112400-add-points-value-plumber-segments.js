'use strict';

module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.addColumn('plumber_segments', 'pointsValue', {
      type: Sequelize.DECIMAL(10, 2),
      allowNull: true,
    });
  },

  down: async queryInterface => {
    await queryInterface.removeColumn('plumber_segments', 'pointsValueInMoney');
  },
};
