'use strict';

module.exports = {
  up: async (queryInterface, Sequelize) => {
    // إضافة العمود referred_user_id
    await queryInterface.addColumn('pending_bonuses', 'referred_user_id', {
      type: Sequelize.INTEGER,
      allowNull: false,
    });

    // إضافة العمود status
    await queryInterface.addColumn('pending_bonuses', 'status', {
      type: Sequelize.ENUM('PENDING', 'COMPLETED'),
      allowNull: false,
      defaultValue: 'PENDING',
    });

  
    await queryInterface.addColumn('pending_bonuses', 'createdAt', {
      type: Sequelize.DATE,
      allowNull: false,
      defaultValue: Sequelize.fn('NOW'),
    });

   
    await queryInterface.addColumn('pending_bonuses', 'updatedAt', {
      type: Sequelize.DATE,
      allowNull: false,
      defaultValue: Sequelize.fn('NOW'),
    });
  },

  down: async (queryInterface) => {
    await queryInterface.removeColumn('pending_bonuses', 'referred_user_id');
    await queryInterface.removeColumn('pending_bonuses', 'status');
    await queryInterface.removeColumn('pending_bonuses', 'createdAt');
    await queryInterface.removeColumn('pending_bonuses', 'updatedAt');
  },
};

