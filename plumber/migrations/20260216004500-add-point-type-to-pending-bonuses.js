'use strict';

module.exports = {
    up: async (queryInterface, Sequelize) => {
        await queryInterface.addColumn('pending_bonuses', 'point_type', {
            type: Sequelize.ENUM('fixed_points', 'instant_withdrawal'),
            allowNull: false,
            defaultValue: 'instant_withdrawal',
        });
    },

    down: async (queryInterface, Sequelize) => {
        await queryInterface.removeColumn('pending_bonuses', 'point_type');
        // Note: Removing ENUM types in MySQL can be tricky, but usually removeColumn is enough 
        // for the column itself. The ENUM type might persist in the DB schema metadata.
    },
};
