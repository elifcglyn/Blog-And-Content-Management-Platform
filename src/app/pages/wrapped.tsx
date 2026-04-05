import * as React from "react";
import { Sparkles, Clock, TrendingUp, Award, ChevronRight, ChevronLeft, X } from "lucide-react";
import { Link } from "react-router";
import { cn } from "../components/ui/utils"; // Kendi utils yoluna göre ayarla

// 📌 Backend'den geldiğini varsaydığımız kullanıcı istatistikleri
const USER_STATS = {
  totalMinutes: 342,
  topCategory: "Teknoloji",
  topCategoryReadCount: 24,
  favoriteAuthor: "Caner Kaya",
  totalPostsRead: 45,
  persona: "Bilgi Avcısı"
};

// 📌 Slaytlarımızın İçerikleri
const SLIDES = [
  {
    id: "intro",
    theme: "from-indigo-600 to-purple-900",
    icon: Sparkles,
    content: (
      <div className="text-center space-y-6 animate-in slide-in-from-bottom-10 fade-in duration-1000">
        <h2 className="text-2xl font-bold tracking-widest text-white/70 uppercase">Nisan 2026 Özeti</h2>
        <h1 className="font-serif text-5xl md:text-7xl italic text-white leading-tight">
          Bu ay kelimelerin içinde kayboldun...
        </h1>
      </div>
    )
  },
  {
    id: "time",
    theme: "from-teal-600 to-emerald-900",
    icon: Clock,
    content: (
      <div className="text-center space-y-8 animate-in zoom-in-95 fade-in duration-1000">
        <h2 className="text-xl font-bold tracking-widest text-white/70 uppercase">Zaman Nasıl Geçti?</h2>
        <div>
          <span className="font-serif text-8xl md:text-9xl italic text-white font-black drop-shadow-2xl">
            {USER_STATS.totalMinutes}
          </span>
          <span className="text-3xl text-white/80 font-light block mt-2">dakika</span>
        </div>
        <p className="text-xl text-teal-100 font-light max-w-md mx-auto">
          Postify'da okuyarak geçirdin. Bu tam 5 saatten fazla saf bilgi demek!
        </p>
      </div>
    )
  },
  {
    id: "category",
    theme: "from-rose-600 to-orange-900",
    icon: TrendingUp,
    content: (
      <div className="text-center space-y-6 animate-in slide-in-from-right-10 fade-in duration-1000">
        <h2 className="text-xl font-bold tracking-widest text-white/70 uppercase">Kalbin Ne İçin Atıyor?</h2>
        <h1 className="font-serif text-6xl md:text-8xl italic text-white leading-tight drop-shadow-2xl">
          {USER_STATS.topCategory}
        </h1>
        <p className="text-xl text-rose-100 font-light max-w-md mx-auto">
          Bu ay en çok bu kategoride dolaştın ve tam <strong className="font-bold text-white">{USER_STATS.topCategoryReadCount}</strong> farklı yazı okudun.
        </p>
      </div>
    )
  },
  {
    id: "outro",
    theme: "from-slate-900 to-black",
    icon: Award,
    content: (
      <div className="text-center space-y-8 animate-in slide-in-from-bottom-10 fade-in duration-1000">
        <div className="w-32 h-32 mx-auto bg-gradient-to-tr from-amber-400 to-orange-600 rounded-full flex items-center justify-center shadow-[0_0_100px_rgba(251,191,36,0.4)]">
          <Award className="w-16 h-16 text-white" />
        </div>
        <div>
          <h2 className="text-xl font-bold tracking-widest text-white/70 uppercase mb-4">Senin Okur Profilin</h2>
          <h1 className="font-serif text-5xl md:text-7xl italic text-transparent bg-clip-text bg-gradient-to-r from-amber-200 to-yellow-500">
            {USER_STATS.persona}
          </h1>
        </div>
        <p className="text-lg text-slate-400 font-light max-w-sm mx-auto">
          Toplam {USER_STATS.totalPostsRead} yazı devirdin. En çok okuduğun yazar ise {USER_STATS.favoriteAuthor} oldu. 
        </p>
        <div className="pt-8">
          <Link to="/" className="inline-block px-8 py-4 bg-white text-slate-900 font-bold rounded-full uppercase tracking-widest hover:scale-105 transition-transform">
            Teşekkürler, Ana Sayfaya Dön
          </Link>
        </div>
      </div>
    )
  }
];

export function WrappedPage() {
  const [currentSlide, setCurrentSlide] = React.useState(0);
  const slide = SLIDES[currentSlide];
  const Icon = slide.icon;

  const nextSlide = () => {
    if (currentSlide < SLIDES.length - 1) setCurrentSlide(prev => prev + 1);
  };

  const prevSlide = () => {
    if (currentSlide > 0) setCurrentSlide(prev => prev - 1);
  };

  // Zamanlayıcı (Story mantığı: Her slayt 6 saniye durur, sonra geçer)
  React.useEffect(() => {
    const timer = setTimeout(() => {
      if (currentSlide < SLIDES.length - 1) {
        nextSlide();
      }
    }, 6000); // 6 saniye
    return () => clearTimeout(timer);
  }, [currentSlide]);

  return (
    <div className={cn("fixed inset-0 z-50 flex flex-col bg-gradient-to-br transition-colors duration-1000", slide.theme)}>
      
      {/* 🌟 ÜST BAR (Kapatma ve İlerleme Çubuğu) */}
      <div className="p-6 md:p-10 flex flex-col gap-4 z-10">
        <div className="flex gap-2 w-full max-w-3xl mx-auto">
          {SLIDES.map((_, idx) => (
            <div key={idx} className="h-1.5 flex-1 bg-white/20 rounded-full overflow-hidden">
              <div 
                className={cn(
                  "h-full bg-white transition-all duration-[6000ms] ease-linear origin-left",
                  idx === currentSlide ? "w-full" : idx < currentSlide ? "w-full !duration-0" : "w-0 !duration-0"
                )}
              />
            </div>
          ))}
        </div>
        <div className="flex justify-between items-center w-full max-w-3xl mx-auto text-white mt-4">
          <div className="flex items-center gap-2 font-bold uppercase tracking-widest text-xs opacity-80">
            <Icon className="w-5 h-5" /> Postify Özet
          </div>
          <Link to="/" className="p-2 bg-white/10 hover:bg-white/20 rounded-full backdrop-blur-md transition-colors">
            <X className="w-5 h-5" />
          </Link>
        </div>
      </div>

      {/* 🌟 İÇERİK ALANI */}
      <div className="flex-1 flex items-center justify-center p-6 relative z-10">
        {/* Önceki Slayta Geçme Alanı (Sol taraf tıklaması) */}
        <button onClick={prevSlide} className="absolute left-0 top-0 bottom-0 w-1/4 z-20 cursor-w-resize opacity-0" />
        
        {/* SLAYT İÇERİĞİ (Key veriyoruz ki her slayt değişiminde animasyon baştan oynasın) */}
        <div key={currentSlide} className="w-full max-w-3xl flex justify-center">
          {slide.content}
        </div>

        {/* Sonraki Slayta Geçme Alanı (Sağ taraf tıklaması) */}
        <button onClick={nextSlide} className="absolute right-0 top-0 bottom-0 w-1/4 z-20 cursor-e-resize opacity-0" />
      </div>

      {/* 🌟 MOBİL İÇİN YÖNLENDİRME BUTONLARI (Opsiyonel) */}
      <div className="p-10 flex justify-between w-full max-w-3xl mx-auto z-10">
        <button onClick={prevSlide} className={cn("p-4 rounded-full bg-white/10 backdrop-blur-md text-white transition-opacity", currentSlide === 0 ? "opacity-0 pointer-events-none" : "hover:bg-white/20")}>
          <ChevronLeft className="w-6 h-6" />
        </button>
        <button onClick={nextSlide} className={cn("p-4 rounded-full bg-white/10 backdrop-blur-md text-white transition-opacity", currentSlide === SLIDES.length - 1 ? "opacity-0 pointer-events-none" : "hover:bg-white/20")}>
          <ChevronRight className="w-6 h-6" />
        </button>
      </div>

    </div>
  );
}