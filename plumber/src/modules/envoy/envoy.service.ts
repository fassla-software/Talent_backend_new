import EnvoySetting from './envoy.model';
import User from '../user/user.model';
import Plumber from '../plumber/plumber.model';
import Trader from '../trader/trader.model';
import HttpError from '../../utils/HttpError';
import bcrypt from 'bcrypt';
import jwt from 'jsonwebtoken';
import { Roles } from '../role/role.model';
import { assignRole } from '../role/role.service';
import { generateUniqueReferralCode } from '../../utils/generateReferCode';
import { PlumberAccountStatus } from '../plumber/plumber.model';
import { TraderActivityStatus } from '../trader/trader.model';
import RegistrationBonusRule from '../user/registration_bonus.model';
import { PendingBonus } from '../user/pending-bonus.model';
import { Op } from 'sequelize';
import SMSSender from '../../utils/smsSender';
import { logStatusChange } from '../statusHistory/status-history.service';
import { ClientType } from '../statusHistory/status-history.model';

export const getEnvoySettingByUserId = async (userId: number) => {
    return await EnvoySetting.findOne({ where: { user_id: userId } });
};

interface IRegisterUserByEnvoy {
    name: string;
    password: string;
    phone: string;
    city: string;
    area: string;
    referralCode?: string;
    nationality_id?: string;
    nationality_image1?: string;
    nationality_image2?: string;
}

export const registerUserByEnvoy = async (
    inspectorId: number,
    newUser: IRegisterUserByEnvoy,
    role: Roles
) => {
    const { name, password, phone, city, area, referralCode, nationality_id, nationality_image1, nationality_image2 } = newUser;

    // Validate role
    if (role !== Roles.PLUMBER && role !== Roles.TRADER) {
        throw new HttpError('Role must be either plumber or trader', 400);
    }

    // Check if phone already exists
    const existingUser = await User.findOne({ where: { phone } });
    if (existingUser) {
        throw new HttpError('Phone number already registered', 400);
    }

    let referrer: User | null = null;

    if (referralCode) {
        referrer = await User.findOne({ where: { refer_code: referralCode } });
        if (!referrer) {
            throw new HttpError('Referral code is invalid', 400);
        }
    }

    const hashedPassword = await bcrypt.hash(password, 10);
    const userReferCode = await generateUniqueReferralCode();

    // Create user with phone verified and active
    const user = await User.create({
        name,
        password: hashedPassword,
        phone,
        is_active: true, // Envoy-registered users are active immediately
        phone_verified_at: new Date(), // Mark phone as verified
        refer_code: userReferCode,
    });

    if (!user) throw new HttpError('User not created', 404);

    let plumber = null;
    let trader = null;

    if (role === Roles.PLUMBER) {
        plumber = await Plumber.create({
            user_id: user.id,
            city,
            area,
            inspector_id: inspectorId,
            status: PlumberAccountStatus.PENDING,
            is_verified: true,
            nationality_id: nationality_id || undefined,
            nationality_image1: nationality_image1 || undefined,
            nationality_image2: nationality_image2 || undefined,
        });

        // Log initial status
        await logStatusChange(
            plumber.id,
            ClientType.PLUMBER,
            null,
            PlumberAccountStatus.PENDING
        );
    } else if (role === Roles.TRADER) {
        trader = await Trader.create({
            user_id: user.id,
            city,
            area,
            inspector_id: inspectorId,
            status: TraderActivityStatus.PENDING,
            is_verified: true,
            nationality_id: nationality_id || undefined,
            nationality_image1: nationality_image1 || undefined,
            nationality_image2: nationality_image2 || undefined,
        });

        // Log initial status
        await logStatusChange(
            trader.id,
            ClientType.TRADER,
            null,
            TraderActivityStatus.PENDING
        );
    }

    const assignedRole = await assignRole(user.id, role);

    // Handle referral bonuses
    const bonusRule = await RegistrationBonusRule.findOne({
        where: {
            start_date: { [Op.lte]: new Date() },
            end_date: { [Op.gte]: new Date() },
        },
        order: [['created_at', 'DESC']],
    });

    if (bonusRule) {
        await PendingBonus.create({
            new_user_id: user.id,
            referrer_id: referrer ? referrer.id : null,
            points: bonusRule.points,
        });

        console.log(
            `Pending bonus (${bonusRule.points}) saved for new user ${user.id} with referrer ${referrer?.id || 'none'}`
        );
    }

    // Generate token for the new user
    const payload = { id: user.id, role };
    const token = jwt.sign(payload, process.env.KEY!);

    try {
        const smsSender = new SMSSender();
        const message = `Welcome to Talanet! You have been registered successfully. Your credentials: Phone: ${phone}, Password: ${password}.`;
        await smsSender.sendMessage(phone, message);
        smsSender
            .checkBalance()
            .then(balance => console.log('SMS Balance:', balance))
            .catch(error => console.error('Error Checking Balance:', error.message));
    } catch (error) {
        console.error('Failed to send welcome SMS:', error);
    }

    if (role === Roles.PLUMBER && plumber) {
        return { token, role: assignedRole, ...user.get(), ...plumber.get() };
    } else if (role === Roles.TRADER && trader) {
        return { token, role: assignedRole, ...user.get(), ...trader.get() };
    } else {
        return { token, role: assignedRole, ...user.get() };
    }
};
