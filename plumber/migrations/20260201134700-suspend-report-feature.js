'use strict';

/** @type {import('sequelize-cli').Migration} */
module.exports = {
    async up(queryInterface, Sequelize) {
        // Add status column to report_visits
        await queryInterface.addColumn('report_visits', 'status', {
            type: Sequelize.ENUM('DRAFT', 'SUBMITTED'),
            allowNull: false,
            defaultValue: 'SUBMITTED',
        });

        // Make visit_result nullable in report_visits
        await queryInterface.changeColumn('report_visits', 'visit_result', {
            type: Sequelize.STRING(100),
            allowNull: true,
        });
    },

    async down(queryInterface, Sequelize) {
        // Revert visit_result to non-nullable
        // Note: This might fail if there are NULL values in the column
        await queryInterface.changeColumn('report_visits', 'visit_result', {
            type: Sequelize.STRING(100),
            allowNull: false,
        });

        // Remove status column
        await queryInterface.removeColumn('report_visits', 'status');

    },
};
