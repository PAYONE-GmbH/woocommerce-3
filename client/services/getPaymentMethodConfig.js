import IconLabel from '../components/IconLabel';

export default function getPaymentMethodConfig(name, label, icon, content = null, additionalOptions = {}) {
    const component = content || <></>;

    return {
        name,
        label: <IconLabel
            text={label}
            icon={icon}
        />,
        ariaLabel: label,
        content: component,
        edit: component,
        canMakePayment: () => true,
        paymentMethodId: name,
        supports: {
            showSavedCards: false,
            showSaveOption: false,
        },
        ...additionalOptions,
    };
}
