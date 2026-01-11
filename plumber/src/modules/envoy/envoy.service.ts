import EnvoySetting from './envoy.model';

export const getEnvoySettingByUserId = async (userId: number) => {
    return await EnvoySetting.findOne({ where: { user_id: userId } });
};
