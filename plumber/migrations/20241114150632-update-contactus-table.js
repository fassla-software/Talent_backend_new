'use strict';

/** @type {import('sequelize-cli').Migration} */
module.exports = {
  async up(queryInterface, Sequelize) {
    await queryInterface.addColumn('contact_us', 'name', {
      type: Sequelize.STRING,
      allowNull: true,
    });
    await queryInterface.addColumn('contact_us', 'message', {
      type: Sequelize.STRING,
      allowNull: true,
    });
  },

  async down(queryInterface) {
    await queryInterface.removeColumn('contact_us', 'name');
    await queryInterface.removeColumn('contact_us', 'message');
  },
};
