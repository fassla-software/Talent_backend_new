'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    // Add plumber_id to inspection_visits
    await queryInterface.addColumn('inspection_visits', 'plumber_id', {
      type: Sequelize.INTEGER.UNSIGNED,
      allowNull: true,
      references: {
        model: 'plumbers',
        key: 'id',
      },
      onUpdate: 'CASCADE',
      onDelete: 'SET NULL',
    });
    await queryInterface.addIndex('inspection_visits', ['plumber_id']);

    // Add plumber_id to report_visits
    await queryInterface.addColumn('report_visits', 'plumber_id', {
      type: Sequelize.INTEGER.UNSIGNED,
      allowNull: true,
      references: {
        model: 'plumbers',
        key: 'id',
      },
      onUpdate: 'CASCADE',
      onDelete: 'SET NULL',
    });
    await queryInterface.addIndex('report_visits', ['plumber_id']);
  },

  async down(queryInterface) {
    await queryInterface.removeIndex('inspection_visits', ['plumber_id']);
    await queryInterface.removeColumn('inspection_visits', 'plumber_id');

    await queryInterface.removeIndex('report_visits', ['plumber_id']);
    await queryInterface.removeColumn('report_visits', 'plumber_id');
  },
};
