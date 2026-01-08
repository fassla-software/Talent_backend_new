module.exports = {
  up: async (queryInterface, Sequelize) => {
    await queryInterface.addColumn('plumber_withdraw_requests', 'deleted_at', {
      type: Sequelize.DATE,
      allowNull: true,
    });
  },

  down: async queryInterface => {
    await queryInterface.removeColumn('plumber_withdraw_requests', 'deleted_at');
  },
};
