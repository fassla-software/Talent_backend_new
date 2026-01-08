'use strict';

module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.addColumn('registration_bonus', 'point_type', {
      type: Sequelize.ENUM('fixed_points', 'instant_withdrawal'),
      allowNull: false,
      defaultValue: 'instant_withdrawal',
    });
  },

  down: async (queryInterface) => {
    await queryInterface.removeColumn('registration_bonus', 'point_type');
  },
};

