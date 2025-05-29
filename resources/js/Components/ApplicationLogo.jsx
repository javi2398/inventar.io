import logo from '../../images/logo.png'; // Usa el alias @ si está configurado

export default function ApplicationLogo({ className = "h-32 w-auto ", ...props }) {
    return (
        <img
            src={logo}
            alt="Logo"
            className={className}
            {...props}
        />
    );
}
