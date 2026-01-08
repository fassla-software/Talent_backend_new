'use strict';

/** @type {import('sequelize-cli').Migration} */
module.exports = {
  async up (queryInterface, Sequelize) {
    await queryInterface.addColumn('inspection_visits', 'status', {
      type: Sequelize.ENUM('PENDING', 'APPROVED', 'REJECTED'),
      allowNull: false,
      defaultValue: 'PENDING',
    });
  },

  async down (queryInterface, Sequelize) {
  	await queryInterface.removeColumn('inspection_visits', 'status');
  }
};
