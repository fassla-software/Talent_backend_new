'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    // Add new columns if they do not exist
    await queryInterface.addColumn('inspection_requests', 'inspection_lat', {
      type: Sequelize.DECIMAL(10, 8),
      allowNull: true,
    });
    await queryInterface.addColumn('inspection_requests', 'inspection_long', {
      type: Sequelize.DECIMAL(11, 8),
      allowNull: true,
    });
    await queryInterface.addColumn('inspection_requests', 'user_lat', {
      type: Sequelize.DECIMAL(10, 8),
      allowNull: true,
    });
    await queryInterface.addColumn('inspection_requests', 'user_long', {
      type: Sequelize.DECIMAL(11, 8),
      allowNull: true,
    });
    await queryInterface.addColumn('inspection_requests', 'inspection_images', {
      type: Sequelize.STRING,
      allowNull: true,
    });
  },

  async down(queryInterface) {
    // Remove columns if rolling back the migration
    await queryInterface.removeColumn('inspection_requests', 'inspection_lat');
    await queryInterface.removeColumn('inspection_requests', 'inspection_long');
    await queryInterface.removeColumn('inspection_requests', 'user_lat');
    await queryInterface.removeColumn('inspection_requests', 'user_long');
    await queryInterface.removeColumn('inspection_requests', 'inspection_images');
  },
};
