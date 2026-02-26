'use strict';

module.exports = {
    async up(queryInterface, Sequelize) {
        await queryInterface.changeColumn('envoy_notes', 'envoy_id', {
            type: Sequelize.BIGINT.UNSIGNED,
            allowNull: true,
        });
    },

    async down(queryInterface, Sequelize) {
        await queryInterface.changeColumn('envoy_notes', 'envoy_id', {
            type: Sequelize.BIGINT.UNSIGNED,
            allowNull: false,
        });
    }
};