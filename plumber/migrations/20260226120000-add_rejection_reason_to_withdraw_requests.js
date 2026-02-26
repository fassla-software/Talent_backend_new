'use strict';

module.exports = {
    up: async (queryInterface, Sequelize) => {
        await queryInterface.addColumn('plumber_withdraw_requests', 'rejection_reason', {
            type: Sequelize.TEXT,
            allowNull: true,
            after: 'status'
        });
    },

    down: async (queryInterface, Sequelize) => {
        await queryInterface.removeColumn('plumber_withdraw_requests', 'rejection_reason');
    },
};
