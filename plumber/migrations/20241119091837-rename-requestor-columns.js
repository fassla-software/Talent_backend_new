'use strict';

module.exports = {
  up: async queryInterface => {
    await queryInterface.renameColumn('inspection_requests', 'requestor_name', 'user_name');
    await queryInterface.renameColumn('inspection_requests', 'requestor_phone', 'user_phone');
  },

  down: async queryInterface => {
    await queryInterface.renameColumn('inspection_requests', 'user_name', 'requestor_name');
    await queryInterface.renameColumn('inspection_requests', 'user_phone', 'requestor_phone');
  },
};
