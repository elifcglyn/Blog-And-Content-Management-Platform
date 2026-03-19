import { useState } from "react";
import { useNavigate } from "react-router";
import { Eye, EyeOff, Loader2, Sparkles } from "lucide-react";
import { Button } from "../components/ui/button";
import { Input } from "../components/ui/input";
import { Label } from "../components/ui/label";

export function AuthPage() {
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [isLoginMode, setIsLoginMode] = useState(true);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [name, setName] = useState(""); // Kayıt modu için

  const handleSubmit = (e: React.FormEvent) => {
  e.preventDefault();
  setIsLoading(true);

  // Arkadaşın backend'i bitirene kadar burası giriş yapmışsın gibi davranacak
  setTimeout(() => {
    setIsLoading(false);
    
    // Tarayıcı hafızasına "giriş yapıldı" biletini bırakıyoruz
    localStorage.setItem("isAuthenticated", "true");
    
    // Kullanıcı adını da saklayalım ki Header'da gösterelim
    localStorage.setItem("userName", isLoginMode ? "Sarah Chen" : name); 

    // İçeri alıyoruz
    navigate("/");
  }, 1000);
};

  return (
    <>
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@400;500;700&display=swap');
        
        .font-serif {
          font-family: 'Instrument Serif', Georgia, serif;
        }
        .font-sans {
          font-family: 'DM Sans', sans-serif;
        }
        @keyframes fade-in-up {
          from { opacity: 0; transform: translateY(16px); }
          to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
          animation: fade-in-up 0.6s ease-out forwards;
        }
      `}</style>

      <div className="min-h-screen w-full flex flex-col md:flex-row bg-background font-sans transition-colors duration-500">
        
        {/* SOL PANEL - Yeni Görsel ve Slogan Arka Planı */}
        <div className="w-full md:w-1/2 p-12 md:p-20 flex flex-col justify-between text-white relative overflow-hidden bg-slate-950">
          
          {/* Gönderdiğin Görseli Arka Plan Olarak Ekliyoruz */}
          <div 
            className="absolute inset-0 z-0 opacity-60"
            style={{ 
              backgroundImage: `url('/Ekran Resmi 2026-03-19 17.40.30.jpg')`,
              backgroundSize: 'cover',
              backgroundPosition: 'center',
            }}
          ></div>
          
          {/* Görselin üzerine hafif bir koyuluk katman (okunabilirlik için) */}
          <div className="absolute inset-0 bg-gradient-to-b from-slate-950/40 via-transparent to-slate-950/60 z-1"></div>

          {/* Logo */}
          <div className="relative z-10 animate-fade-in-up flex items-center gap-3">
             <div className="p-2.5 bg-white/10 rounded-xl border border-white/20 backdrop-blur-md text-teal-300">
                <Sparkles className="w-6 h-6" />
             </div>
            <h1 className="font-serif text-5xl italic tracking-[-0.02em] text-white">Postify</h1>
          </div>

          {/* Orta Kısım - Slogan */}
          <div className="relative z-10 max-w-lg mt-16 md:mt-0 animate-fade-in-up" style={{ animationDelay: "0.2s" }}>
            <h2 className="font-serif text-7xl italic leading-[1] mb-6 text-white tracking-[-0.03em]">
              Söz uçar,<br />
              <span className="text-teal-300">versiyon</span> kalır.
            </h2>
            <p className="text-slate-200 text-lg max-w-sm leading-relaxed mt-6 font-light">
              Düşüncelerinin her adımını kaydet, mükemmelliğe giden yolu Postify ile belgele.
            </p>
          </div>

          {/* Alt Kısım */}
          <div className="relative z-10 mt-16 md:mt-0 text-sm text-slate-300 animate-fade-in-up font-light" style={{ animationDelay: "0.4s" }}>
            <span>© 2026 Postify Platform. Modern yazarlar için versiyon kontrollü blog sistemi.</span>
          </div>
        </div>

        {/* SAĞ PANEL - Giriş / Kayıt Formu */}
        <div className="w-full md:w-1/2 flex flex-col relative p-8 sm:p-16 lg:p-24 justify-center items-center bg-background">
          <div className="w-full max-w-[360px] space-y-8 animate-fade-in-up" style={{ animationDelay: "0.1s" }}>
            <div>
              <h2 className="font-serif text-5xl italic text-foreground tracking-[-0.03em] mb-3">
                {isLoginMode ? "Hoş Geldin" : "Hemen Başla"}
              </h2>
              <p className="text-sm text-muted-foreground">
                {isLoginMode 
                  ? "Hesabınıza erişmek için bilgilerinizi doğrulayın" 
                  : "Postify dünyasına katılmak için yeni bir hesap oluşturun"}
              </p>
            </div>
            
            <form onSubmit={handleSubmit} className="space-y-4">
              {!isLoginMode && (
                <div className="space-y-1.5">
                  <Label htmlFor="name">Ad Soyad</Label>
                  <Input id="name" type="text" placeholder="Elif Yılmaz" required />
                </div>
              )}

              <div className="space-y-1.5">
                <Label htmlFor="email">E-posta</Label>
                <Input id="email" type="email" placeholder="ornek@sirket.com" required />
              </div>

              <div className="space-y-1.5">
                <div className="flex items-center justify-between mb-1.5">
                  <Label htmlFor="password">Şifre</Label>
                  {isLoginMode && (
                    <a href="#" className="text-xs text-teal-600 hover:underline" onClick={(e) => e.preventDefault()}>
                      Şifremi unuttum?
                    </a>
                  )}
                </div>
                <div className="relative">
                  <Input
                    id="password"
                    type={showPassword ? "text" : "password"}
                    placeholder="••••••••"
                    required
                    className="pr-10"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                  >
                    {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                  </button>
                </div>
              </div>

              <Button type="submit" className="w-full h-11 mt-5 bg-teal-600 hover:bg-teal-700 text-white shadow-lg shadow-teal-600/20" disabled={isLoading}>
                {isLoading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                {isLoginMode ? "Giriş Yap" : "Hesap Oluştur"}
              </Button>
            </form>

            <div className="text-center text-sm pt-6 border-t border-border">
              <span className="text-muted-foreground">
                {isLoginMode ? "Hesabınız yok mu? " : "Zaten bir hesabınız var mı? "}
              </span>
              <button
                type="button"
                onClick={() => setIsLoginMode(!isLoginMode)}
                className="font-medium text-teal-600 hover:underline underline-offset-4"
              >
                {isLoginMode ? "Hesap oluşturun" : "Giriş yapın"}
              </button>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}