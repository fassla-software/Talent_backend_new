import ClientStatusHistory, { ClientType } from './status-history.model';

/**
 * Log a status change for a client (Trader or Plumber)
 * 
 * @param clientId - ID of the trader or plumber
 * @param clientType - Type of client (TRADER or PLUMBER)
 * @param oldStatus - Previous status
 * @param newStatus - New status
 */
export const logStatusChange = async (
    clientId: number,
    clientType: ClientType,
    oldStatus: string | null,
    newStatus: string
) => {
    // Only log if status actually changed
    if (oldStatus === newStatus) {
        return null;
    }

    return await ClientStatusHistory.create({
        client_id: clientId,
        client_type: clientType,
        old_status: oldStatus,
        new_status: newStatus,
        changed_at: new Date(),
    });
};
