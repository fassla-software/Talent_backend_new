'use strict';

module.exports = {
    async up(queryInterface, Sequelize) {
        // Add inspector_id to plumbers table
        await queryInterface.addColumn('plumbers', 'inspector_id', {
            type: Sequelize.BIGINT.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'id',
            },
            onUpdate: 'CASCADE',
            onDelete: 'SET NULL',
        });
        await queryInterface.addIndex('plumbers', ['inspector_id']);

        // Add status to plumbers table
        await queryInterface.addColumn('plumbers', 'status', {
            type: Sequelize.ENUM('ACTIVE', 'INACTIVE', 'DORMANT', 'PENDING'),
            allowNull: false,
            defaultValue: 'PENDING',
        });
    },

    async down(queryInterface) {
        // Remove columns from plumbers table
        await queryInterface.removeIndex('plumbers', ['inspector_id']);
        await queryInterface.removeColumn('plumbers', 'inspector_id');
        await queryInterface.removeColumn('plumbers', 'status');
    },
};
