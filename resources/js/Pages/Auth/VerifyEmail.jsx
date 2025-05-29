import PrimaryButton from '@/Components/PrimaryButton';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function VerifyEmail({ status }) {
    const { post, processing } = useForm({});

    const submit = (e) => {
        e.preventDefault();
        post(route('verification.send'));
    };

    return (
        <GuestLayout>
            <Head title="Verificación de Correo | Inventar.io" />

            <div className="max-w-md mx-auto mt-12 bg-slate-800 rounded-xl shadow-xl overflow-hidden border border-slate-700">
                <div className="bg-slate-900 text-center p-6 border-b border-slate-700">
                    <h1 className="text-2xl font-semibold text-cyan-400">Inventar.io</h1>
                    <p className="text-slate-400 text-sm mt-1">Verifica tu dirección de correo</p>
                </div>

                <div className="p-6 text-slate-200 text-sm leading-relaxed">
                    <p className="mb-4">
                        Gracias por registrarte. Antes de comenzar, por favor verifica tu dirección de correo
                        haciendo clic en el enlace que te acabamos de enviar. Si no lo recibiste, con gusto te
                        enviaremos otro.
                    </p>

                    {status === 'verification-link-sent' && (
                        <div className="mb-4 p-3 bg-emerald-600/20 text-emerald-300 rounded border border-emerald-500 text-sm">
                            Se ha enviado un nuevo enlace de verificación a tu correo.
                        </div>
                    )}

                    <form onSubmit={submit}>
                        <div className="mt-6 flex items-center justify-between">
                            <PrimaryButton disabled={processing}>
                                Reenviar correo de verificación
                            </PrimaryButton>

                            <Link
                                href={route('logout')}
                                method="post"
                                as="button"
                                className="text-sm text-slate-400 underline hover:text-cyan-300"
                            >
                                Cerrar sesión
                            </Link>
                        </div>
                    </form>
                </div>

                <div className="bg-slate-900 p-4 text-center text-xs text-slate-500 border-t border-slate-700">
                    © {new Date().getFullYear()} Inventar.io. Todos los derechos reservados. <br />
                    <a href="/" className="text-cyan-400 hover:underline">www.inventar.io</a>
                </div>
            </div>
        </GuestLayout>
    );
}
