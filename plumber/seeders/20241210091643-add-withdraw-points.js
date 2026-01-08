'use strict';

module.exports = {
  async up(queryInterface, Sequelize) {
    await queryInterface.bulkInsert('app_configs', [
      {
        id: 1,
        key: 'withdraw_points',
        value: 2,
        created_at: new Date(),
        updated_at: new Date(),
      },
    ]);
  },

  async down(queryInterface, Sequelize) {
    await queryInterface.bulkDelete('app_configs', { id: 1 }, {});
  },
};
