'use strict';

module.exports = {
    up: async (queryInterface, Sequelize) => {
        await queryInterface.createTable('client_status_history', {
            id: {
                type: Sequelize.INTEGER,
                primaryKey: true,
                autoIncrement: true,
            },
            client_id: {
                type: Sequelize.INTEGER,
                allowNull: false,
                comment: 'ID of trader or plumber',
            },
            client_type: {
                type: Sequelize.ENUM('TRADER', 'PLUMBER'),
                allowNull: false,
            },
            old_status: {
                type: Sequelize.STRING(50),
                allowNull: true,
                comment: 'Previous status (null for first record)',
            },
            new_status: {
                type: Sequelize.STRING(50),
                allowNull: false,
            },
            changed_at: {
                type: Sequelize.DATE,
                allowNull: false,
                defaultValue: Sequelize.literal('CURRENT_TIMESTAMP'),
            },
            created_at: {
                type: Sequelize.DATE,
                allowNull: false,
                defaultValue: Sequelize.literal('CURRENT_TIMESTAMP'),
            },
            updated_at: {
                type: Sequelize.DATE,
                allowNull: false,
                defaultValue: Sequelize.literal('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            },
        });

        // Add indexes for performance
        await queryInterface.addIndex('client_status_history', ['client_type', 'client_id'], {
            name: 'idx_client',
        });
        await queryInterface.addIndex('client_status_history', ['changed_at'], {
            name: 'idx_changed_at',
        });
        await queryInterface.addIndex('client_status_history', ['new_status'], {
            name: 'idx_new_status',
        });
    },

    down: async (queryInterface, Sequelize) => {
        await queryInterface.dropTable('client_status_history');
    },
};
