'use strict';

module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.removeConstraint('pending_bonuses', 'pending_bonuses_ibfk_1');

    await queryInterface.renameColumn('pending_bonuses', 'user_id', 'new_user_id');

    await queryInterface.changeColumn('pending_bonuses', 'new_user_id', {
      type: Sequelize.INTEGER,
      allowNull: false,
    });

    await queryInterface.addConstraint('pending_bonuses', {
      fields: ['new_user_id'],
      type: 'foreign key',
      name: 'fk_pending_bonuses_new_user_id',   
      references: {
        table: 'users',
        field: 'id',
      },
      onUpdate: 'CASCADE',
      onDelete: 'CASCADE',
    });
  },

  down: async (queryInterface, Sequelize) => {
    await queryInterface.removeConstraint('pending_bonuses', 'fk_pending_bonuses_new_user_id');

    await queryInterface.renameColumn('pending_bonuses', 'new_user_id', 'user_id');

    await queryInterface.changeColumn('pending_bonuses', 'user_id', {
      type: Sequelize.INTEGER,
      allowNull: true,  
    });

    await queryInterface.addConstraint('pending_bonuses', {
      fields: ['user_id'],
      type: 'foreign key',
      name: 'pending_bonuses_ibfk_1',
      references: {
        table: 'users',
        field: 'id',
      },
      onUpdate: 'CASCADE',
      onDelete: 'CASCADE',
    });
  },
};
