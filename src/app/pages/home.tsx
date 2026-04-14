
import { Link } from "react-router";
import { Sparkles, TrendingUp, Clock, ChevronRight } from "lucide-react";
import { mockPosts } from "../data/mock-data";
import { PostCard } from "../components/post-card";
import { BookmarkButton } from "../components/bookmark-button";
import React, { useState, useEffect } from "react";

// 📌 DEV KATEGORİ LİSTESİ (Gerçek bir platform standartı)
const CATEGORIES = [
  { id: "all", label: "Sana Özel" },
  { id: "yazilim", label: "Yazılım" },
  { id: "teknoloji", label: "Teknoloji" },
  { id: "girisimcilik", label: "Girişimcilik & İş" },
  { id: "kisisel-gelisim", label: "Kişisel Gelişim" },
  { id: "saglik", label: "Sağlık & Yaşam" },
  { id: "bilim", label: "Bilim" },
  { id: "finans", label: "Finans & Ekonomi" },
  { id: "sanat", label: "Sanat & Tasarım" },
  { id: "edebiyat", label: "Kitap & Edebiyat" },
  { id: "seyahat", label: "Seyahat" },
  { id: "spor", label: "Spor" },
  { id: "yemek", label: "Gastronomi" },
  { id: "sinema", label: "Sinema & TV" },
  { id: "oyun", label: "Oyun & E-Spor" },
  { id: "moda", label: "Moda" },
  { id: "tarih", label: "Tarih" },
  { id: "kripto", label: "Web3 & Kripto" }
];

export function HomePage() {
  // 🚀 State Yönetimi
  const [activeCategory, setActiveCategory] = React.useState("all");
  const [posts, setPosts] = React.useState<any[]>([]);

  // 🌐 Veritabanından Veri Çekme ve Formatlama
  React.useEffect(() => {
    fetch("http://localhost/Blog-And-Content-Management-Platform/api/yazilari_getir.php")
      .then((res) => res.json())
      .then((data) => {
        const formatliVeri = data.map((item: any) => ({
          id: item.id.toString(),
          slug: item.id.toString(),
          title: item.baslik,
          excerpt: item.ozet,
          category: "all",
          coverImage: item.kapak_resmi || "https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=2000",
          date: item.yayin_tarihi,
          readTime: "3 min read",
          author: { name: "Emirhan", avatar: "https://ui-avatars.com/api/?name=Emirhan" }
        }));
        setPosts(formatliVeri);
      })
      .catch((err) => console.error("Veri çekme hatası:", err));
  }, []);

  // 🛡️ Güvenlik Ağı: Veri gelene kadar veya hata olursa sahte verileri göster
  const dataToRender = posts.length > 0 ? posts : mockPosts;

  // 🚀 Filtreleme Mantığı
  const filteredPosts = activeCategory === "all" 
    ? dataToRender 
    : dataToRender.filter((post: any) => post.category === activeCategory);

  // Filtrelenmiş listeye göre güncel yazılar
  const featuredPost = filteredPosts[0];
  const remainingPosts = filteredPosts.slice(1);

  
  return (
  
    <div className="flex-1 flex flex-col bg-white dark:bg-slate-950 font-sans overflow-auto animate-in fade-in duration-700">
      
      <main className="container mx-auto max-w-7xl py-12 px-6">
        
        {/* 🌟 YENİ: SPOTIFY WRAPPED TARZI ÖZET BANNER'I */}
        <div className="mb-12 relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-indigo-900 via-purple-900 to-slate-900 p-8 md:p-10 shadow-2xl shadow-purple-500/20 group cursor-default">
          {/* Arkaplan Parıltı Efektleri */}
          <div className="absolute -top-24 -right-24 w-64 h-64 bg-purple-500/30 blur-3xl rounded-full group-hover:bg-purple-400/40 transition-colors duration-700" />
          <div className="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500/30 blur-3xl rounded-full group-hover:bg-indigo-400/40 transition-colors duration-700" />

          <div className="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div className="text-center md:text-left space-y-3">
              <div className="flex items-center justify-center md:justify-start gap-2 text-purple-300 font-bold text-xs uppercase tracking-widest">
                <Sparkles className="w-4 h-4 animate-pulse" /> <span>Postify Wrapped</span>
              </div>
              <h2 className="font-serif text-3xl md:text-4xl italic text-white leading-tight">
                Aylık Okuma Özetin Hazır!
              </h2>
              <p className="text-purple-200/80 font-light max-w-md">
                Bu ay platformda ne kadar zaman geçirdin, en çok hangi kategorileri tükettin? Senin için hazırladığımız o büyüleyici hikayeye göz at.
              </p>
            </div>

            <Link
              to="/wrapped"
              className="shrink-0 group/btn inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-indigo-950 rounded-full font-bold uppercase tracking-widest hover:scale-105 hover:shadow-[0_0_40px_rgba(255,255,255,0.4)] transition-all duration-300"
            >
              Özetimi Keşfet 
              <ChevronRight className="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" />
            </Link>
          </div>
        </div>

        {/* 🎛️ Kompakt Kategori Filtre Çubuğu (Dropdown) */}
        <div className="mb-12 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-6">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-teal-50 dark:bg-teal-900/30 rounded-xl">
              <TrendingUp className="w-5 h-5 text-teal-600 dark:text-teal-400" />
            </div>
            <span className="font-bold text-sm uppercase tracking-widest text-slate-900 dark:text-white">
              Akışı Filtrele
            </span>
          </div>
          
          <div className="relative group w-full sm:w-auto min-w-[240px]">
            <select
              value={activeCategory}
              onChange={(e) => setActiveCategory(e.target.value)}
              className="w-full appearance-none bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl px-6 py-3.5 text-sm font-bold text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-teal-500/50 transition-all cursor-pointer hover:border-teal-300 dark:hover:border-teal-700 shadow-sm"
            >
              {CATEGORIES.map((cat) => (
                <option key={cat.id} value={cat.id} className="font-medium">
                  {cat.label}
                </option>
              ))}
            </select>
            {/* Şık Ok İkonu */}
            <div className="pointer-events-none absolute inset-y-0 right-5 flex items-center text-slate-400 group-hover:text-teal-500 transition-colors">
              <svg className="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
            </div>
          </div>
        </div>

        {/* EĞER SEÇİLEN KATEGORİDE YAZI VARSA GÖSTER */}
        {featuredPost ? (
          <>
            {/* HAFTANIN HİKAYESİ (Featured) */}
            <section className="mb-20">
              <div className="flex items-center gap-2 text-teal-600 font-bold text-xs uppercase tracking-[0.2em] mb-6">
                <Sparkles className="w-4 h-4" /> 
                <span>{activeCategory === "all" ? "Haftanın Hikayesi" : `${CATEGORIES.find(c => c.id === activeCategory)?.label} Gündemi`}</span>
              </div>
              <BookmarkButton />
              <Link 
                to={`/post/${featuredPost.slug}`}
                className="group cursor-pointer grid grid-cols-1 lg:grid-cols-12 gap-10 items-center"
              >
                <div className="lg:col-span-7 rounded-[3rem] overflow-hidden shadow-2xl shadow-teal-500/5 aspect-[16/9] relative">
                  <img 
                    src={featuredPost.coverImage} 
                    alt={featuredPost.title}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" 
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent" />
                </div>
                
                <div className="lg:col-span-5 space-y-6">
                  <h2 className="font-serif text-5xl md:text-6xl italic leading-[1.1] tracking-tighter text-slate-900 dark:text-white group-hover:text-teal-600 transition-colors">
                    {featuredPost.title}
                  </h2>
                  <p className="text-slate-500 text-lg leading-relaxed font-light line-clamp-3 italic">
                    {featuredPost.excerpt}
                  </p>
                  <div className="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-800 w-fit">
                    <span className="text-sm font-bold uppercase tracking-widest text-slate-900 dark:text-white flex items-center gap-2">
                      Devamını Oku <ChevronRight className="w-4 h-4" />
                    </span>
                  </div>
                </div>
              </Link>
            </section>

            {/* DİĞER HİKAYELER */}
            {remainingPosts.length > 0 && (
              <section>
                <div className="flex items-center justify-between mb-10 border-b border-slate-100 dark:border-slate-800 pb-6">
                  <div className="flex items-center gap-2 text-slate-900 dark:text-white font-bold text-sm uppercase tracking-widest">
                    <TrendingUp className="w-5 h-5 text-teal-600" /> <span>Popüler Akış</span>
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-16">
                  {remainingPosts.map((post) => (
                    <Link key={post.id} to={`/post/${post.slug}`} className="transition-transform hover:-translate-y-1 duration-300">
                      <PostCard {...post} />
                    </Link>
                  ))}
                </div>
              </section>
            )}
          </>
        ) : (
          /* EĞER SEÇİLEN KATEGORİ BOŞSA GÖSTERİLECEK EKRAN */
          <div className="py-32 flex flex-col items-center justify-center text-center bg-slate-50 dark:bg-slate-900/30 rounded-[3rem] border border-dashed border-slate-200 dark:border-slate-800">
            <Sparkles className="w-12 h-12 text-slate-300 dark:text-slate-700 mb-6" />
            <h3 className="text-2xl font-serif italic text-slate-900 dark:text-white mb-2">Buralar Henüz Sessiz</h3>
            <p className="text-slate-500">Bu kategoride henüz bir hikaye paylaşılmamış.</p>
          </div>
        )}
      </main>
    </div>
  );
}