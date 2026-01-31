'use strict';

/** @type {import('sequelize-cli').Migration} */
module.exports = {
    async up(queryInterface, Sequelize) {
        // Add scheduled_at column
        await queryInterface.addColumn('inspection_visits', 'scheduled_at', {
            type: Sequelize.DATE,
            allowNull: true,
        });

        // Add visit_type column
        await queryInterface.addColumn('inspection_visits', 'visit_type', {
            type: Sequelize.ENUM('REGULAR', 'FOLLOW_UP'),
            allowNull: false,
            defaultValue: 'REGULAR',
        });

        // Add notes column
        await queryInterface.addColumn('inspection_visits', 'notes', {
            type: Sequelize.TEXT,
            allowNull: true,
        });

        // Update status enum to include SCHEDULED
        await queryInterface.changeColumn('inspection_visits', 'status', {
            type: Sequelize.ENUM('PENDING', 'APPROVED', 'REJECTED', 'SCHEDULED'),
            allowNull: false,
            defaultValue: 'PENDING',
        });
    },

    async down(queryInterface, Sequelize) {
        await queryInterface.changeColumn('inspection_visits', 'status', {
            type: Sequelize.ENUM('PENDING', 'APPROVED', 'REJECTED'),
            allowNull: false,
            defaultValue: 'PENDING',
        });

        await queryInterface.removeColumn('inspection_visits', 'notes');
        await queryInterface.removeColumn('inspection_visits', 'visit_type');
        await queryInterface.removeColumn('inspection_visits', 'scheduled_at');
    }
};
