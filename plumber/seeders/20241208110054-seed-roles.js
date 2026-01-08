'use strict';

module.exports = {
  async up(queryInterface) {
    try {
      await queryInterface.bulkInsert('roles', [
        {
          name: 'plumber',
          guard_name: 'web',
          is_shop: 0,
          created_at: new Date(),
          updated_at: new Date(),
        },
        {
          name: 'envoy',
          guard_name: 'web',
          is_shop: 0,
          created_at: new Date(),
          updated_at: new Date(),
        },
      ]);
    } catch (error) {
      console.error('Error seeding roles:', error);
    }
  },

  async down(queryInterface) {
    await queryInterface.bulkDelete('roles', null, {});
  },
};
