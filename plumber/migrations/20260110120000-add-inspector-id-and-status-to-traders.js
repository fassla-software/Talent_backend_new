'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    // Add inspector_id to traders table
    await queryInterface.addColumn('traders', 'inspector_id', {
      type: Sequelize.BIGINT.UNSIGNED,
      allowNull: true,
      references: {
        model: 'users',
        key: 'id',
      },
      onUpdate: 'CASCADE',
      onDelete: 'SET NULL',
    });
    await queryInterface.addIndex('traders', ['inspector_id']);

    // Add status enum to traders table
    await queryInterface.addColumn('traders', 'status', {
      type: Sequelize.ENUM('ACTIVE', 'INACTIVE', 'DORMANT', 'PENDING'),
      allowNull: false,
      defaultValue: 'PENDING',
    });
  },

  async down(queryInterface) {
    await queryInterface.removeIndex('traders', ['inspector_id']);
    await queryInterface.removeColumn('traders', 'inspector_id');
    await queryInterface.removeColumn('traders', 'status');
  },
};
