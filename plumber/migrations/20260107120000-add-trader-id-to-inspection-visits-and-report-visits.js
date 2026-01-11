'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    // Add trader_id to inspection_visits
    await queryInterface.addColumn('inspection_visits', 'trader_id', {
      type: Sequelize.BIGINT.UNSIGNED, // يتطابق مع traders.id
      allowNull: true,
      references: {
        model: 'traders',
        key: 'id',
      },
      onUpdate: 'CASCADE',
      onDelete: 'SET NULL',
    });
    await queryInterface.addIndex('inspection_visits', ['trader_id']);

    // Add trader_id to report_visits
    await queryInterface.addColumn('report_visits', 'trader_id', {
      type: Sequelize.BIGINT.UNSIGNED, // يتطابق مع traders.id
      allowNull: true,
      references: {
        model: 'traders',
        key: 'id',
      },
      onUpdate: 'CASCADE',
      onDelete: 'SET NULL',
    });
    await queryInterface.addIndex('report_visits', ['trader_id']);
  },

  async down(queryInterface) {
    await queryInterface.removeIndex('inspection_visits', ['trader_id']);
    await queryInterface.removeColumn('inspection_visits', 'trader_id');

    await queryInterface.removeIndex('report_visits', ['trader_id']);
    await queryInterface.removeColumn('report_visits', 'trader_id');
  },
};
